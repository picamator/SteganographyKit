<?php
/**
 * Stego System LSB
 * Least Significant Bit
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoSystem;
use SteganographyKit\SecretText\SecretTextInterface;
use SteganographyKit\SecretText\AbstractSecretText;
use SteganographyKit\StegoText\StegoTextInterface;
use SteganographyKit\CoverText\CoverTextInterface;

class Lsb extends AbstractStegoSystem
{ 
    /**
     * Used channels during encode and decoding
     * Ordering os important
     * 
     * @var array
     */
    protected $useChannel = array(
        'red', 'green', 'blue'
    );
    
    /**
     * Encode secretText
     * 
     * @param   SecretTextInterface $secretText
     * @param   CoverTextInterface  $coverText
     * @return  string
     */
    public function encode(SecretTextInterface $secretText, 
        CoverTextInterface $coverText
    ) {
        // validate
        $useChannelSize = count($this->useChannel);
        $this->validateEncode($secretText, $coverText, $useChannelSize);
        
        // convert secret data to binary
        $secretData = $secretText->getBinaryData();        
        $imageSize  = $coverText->getImageSize();
        
        $imageCoordinate    = array('x' => 0, 'y' => 0);
        $xMaxIndex          = $imageSize['width'] - 1;   
        
        // get current secret text item        
        $secretItem     = array_shift($secretData);
        $secretItem     = $this->splitSecretTextItem($secretItem, $useChannelSize);        
        do {
            $secretDataSize = count($secretData);
            foreach($secretItem as $item) {
                $itemSize = count($item);
                if ($itemSize < $useChannelSize) {
                    // secret item does not have enough data
                    // get next item
                    $secretItem  = array_shift($secretData);
                    
                    // get nessesary bits from next item to full fill last previous one   
                    $chunkSize   = $useChannelSize - $itemSize;
                    $secretChunk = substr($secretItem, 0, $chunkSize);                    
                    $item = array_merge_recursive($item, str_split($secretChunk));
                    
                    // update secretItem
                    $secretItem = substr($secretItem, $chunkSize);
                    $secretItem = $this->splitSecretTextItem($secretItem, $useChannelSize);
                }
                                
                // encode item
                $this->encodeItem($imageCoordinate, $coverText, $item);
                
                // move to next coordinate
                $imageCoordinate = $this->getNextImageCoordinate(
                    $imageCoordinate, $xMaxIndex);  
            }     
            
            // move to next item of secretData of it's size was not changed
            if ($secretDataSize === count($secretData)) {
                $secretItem = array_shift($secretData);
                $secretItem = $this->splitSecretTextItem($secretItem, $useChannelSize);
            }       
        } while($secretDataSize !== 0);    
                
        // save StegoText
        return $coverText->save();
    }
    
    /**
     * Decode stegoText
     * 
     * @param   StegoTextInterface $stegoText
     * @return  string
     */
    public function decode(StegoTextInterface $stegoText) 
    {        
        $imageSize      = $stegoText->getImageSize();
        $endMarkSize    = strlen(AbstractSecretText::END_TEXT_MARK);   
        
        $imageCoordinate    = array('x' => 0, 'y' => 0);
        $xMaxIndex          = $imageSize['width'] - 1;   
        $yMaxIndex          = $imageSize['height'] - 1;   
        $secretText         = '';
        do {
            // get lasts bits value of pixel accordingly confugurated channel
            $secretText .= $this->decodeItem($imageCoordinate, $stegoText);
            $endMark     = substr($secretText, -$endMarkSize, $endMarkSize);
            
            // get next pixel
            $imageCoordinate = $this->getNextImageCoordinate($imageCoordinate, $xMaxIndex);           
        } while ($endMark !== AbstractSecretText::END_TEXT_MARK
            && $imageCoordinate['x'] !== $xMaxIndex && $imageCoordinate['y'] !== $yMaxIndex
        );
             
        var_dump($secretText);

        // remove endText mark
        $cutEndMark = strlen($secretText) % $endMarkSize;
        $cutEndMark = ($cutEndMark === 0)? $endMarkSize: $cutEndMark;
        $secretText = substr($secretText, 0, -$cutEndMark);
        
        // decode
        $secretText = str_split($secretText, $endMarkSize);
        $secretText = array_map('bindec', $secretText);
        $secretText = array_map('chr', $secretText);
                
        return implode('', $secretText);
    }
    
    /**
     * Split secret text item into array chanks accodingly channals number
     * 
     * @param string    $secretItem
     * @param integer   $size
     * @return array
     * <code>
     *  array(
     *      0 => array(
     *          0 => 1,
     *          1 => 0,
     *          2 => 0
     *      ),
     *      ...
     * );
     * </code>
     */
    protected function splitSecretTextItem($secretItem, $size) 
    {
        $secretItem = str_split($secretItem);
        
        return array_chunk($secretItem, $size);
    }
    
    /**
     * 
     * @param array     $imageCoordinate
     * @param integer   $xMaxIndex
     * @return int
     */
    protected function getNextImageCoordinate(array $imageCoordinate, $xMaxIndex) 
    {
        $imageCoordinate['x']++;
        if ($imageCoordinate['x'] > $xMaxIndex) {
            $imageCoordinate['x'] = 0;
            $imageCoordinate['y']++;
        } 
                
        return $imageCoordinate;
    }
    
    /**
     * Encode secret text item
     * 
     * @param array                 $imageCoordinate - e.g. array('x' => 0, 'y' => 0)
     * @param CoverTextInterface    $coverText
     * @param array                 $secretItem - e.g. array(1, 0, 0)
     */
    protected function encodeItem(array $imageCoordinate, 
        CoverTextInterface $coverText, array $secretItem
    ) {           
        // get original pixel in binary
        $originalPixel = $coverText->getBinaryData(
            $imageCoordinate['x'], $imageCoordinate['y']);
        
        // modify configured chanells
        $modifiedPixel = array();
        foreach($this->useChannel as $key => $value) {
            if($secretItem[$key] === '') {
                break;
            }
            // modify original pixel by secret text
            $modifiedPixel[$value] = substr($originalPixel[$value], 0, -1) 
                . $secretItem[$key];
        }
        
        $modifiedPixel = array_merge($originalPixel, $modifiedPixel);        
        $modifiedPixel = array_map('bindec', $modifiedPixel);
        
        // apply modification
        $image = $coverText->getImage();
        $color = imagecolorallocate(
            $image, 
            $modifiedPixel['red'], 
            $modifiedPixel['green'], 
            $modifiedPixel['blue']
        ); 
        imagesetpixel(
            $image,
            $imageCoordinate['x'],
            $imageCoordinate['y'],    
            $color
        );
    }
    
    /**
     * Decode item
     * 
     * @param array                 $imageCoordinate - e.g. array('x' => 0, 'y' => 0)
     * @param StegoTextInterface    $stegoText
     * @return string
     */
    protected function decodeItem(array $imageCoordinate,
        StegoTextInterface $stegoText 
    ) {
        $pixelData = $stegoText->getBinaryData(
            $imageCoordinate['x'], 
            $imageCoordinate['y']
        );
            
//        var_dump($pixelData);
        
        $result = '';
        foreach($this->useChannel as $item) {
            $result .= substr($pixelData[$item], -1, 1);
        }
        
        return $result;
    }
}