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
                    $secretItem = array_shift($secretData);
                    
                    // get nessesary bits from next item to full fill last previous one   
                    $chunkSize   = $useChannelSize - $itemSize;
                    $secretChunk = substr($secretItem, 0, $chunkSize);
                    $item = array_merge_recursive($item, str_split($secretChunk));
                    
                    // update secretItem
                    $secretItem = substr($secretItem, $chunkSize -1);
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
        } while(!empty($secretData));    
        
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
        // @TODO
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
        if ($imageCoordinate['x'] > $xMaxIndex) {
            $imageCoordinate['x'] = 0;
            $imageCoordinate['y']++;
        } else {
            $imageCoordinate['x']++;
        }
        
        return $imageCoordinate;
    }
    
    /**
     * Encode 3 bits of secret text
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
}