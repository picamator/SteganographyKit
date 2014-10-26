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
     * Used channels for encode - decode
     * with a certain order
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
        
        $imageSize          = $coverText->getImageSize();   
        $coordinate         = array('x' => 0, 'y' => 0);
        $xMax               = $imageSize['width'] - 1;
        $secretDataSize     = strlen($secretData);
        
        // encode
        for ($i = 0; $i <= $secretDataSize; $i = $i + $useChannelSize) {
            // get item
            $secretItem = substr($secretData, $i, $useChannelSize);
            // encode item
            $this->encodeItem($coordinate, $coverText, $secretItem);
            // move to next coordinate
            $coordinate = self::getNextCoordinate($coordinate, $xMax);        
        }

        // save StegoText
        return $coverText->save();
    }
    
    /**
     * Decode stegoText
     * 
     * @param   StegoTextInterface $stegoText
     * @param   SecretTextInterface $secretText
     * @return  string
     */
    public function decode(StegoTextInterface $stegoText, 
        SecretTextInterface $secretText
    ) {        
        $imageSize      = $stegoText->getImageSize(); 
        $coordinate     = array('x' => 0, 'y' => 0);
        $xMax           = $imageSize['width'] - 1;   
        $yMax           = $imageSize['height'] - 1;   
        $result         = '';        
        do {
            // get lasts bits value of pixel accordingly confugurated channel
            $result .= $this->decodeItem($coordinate, $stegoText);
            $endMarkPos  = $secretText->getEndMarkPos($result);
                             
            // get next pixel
            $coordinate = self::getNextCoordinate($coordinate, $xMax);           
        } while ($endMarkPos === false
            && ($coordinate['x'] !== $xMax || $coordinate['y'] !== $yMax)
        );
        
        // handle last pixel
        if($endMarkPos === false) {
            $result     .= $this->decodeItem($coordinate, $stegoText);
            $endMarkPos  = $secretText->getEndMarkPos($result);
        }
        
        return $secretText->getFromBinaryData($result, $endMarkPos);
    }
    
    /**
     * Gets next image coordinate
     * 
     * @param array     $coordinate
     * @param integer   $xMax
     * @return integer
     */
    static protected function getNextCoordinate(array $coordinate, $xMax) 
    {
        $coordinate['x']++;
        if ($coordinate['x'] > $xMax) {
            $coordinate['x'] = 0;
            $coordinate['y']++;
        } 
           
        return $coordinate;
    }
    
    /**
     * Encode secret text item
     * 
     * @param array                 $coordinate - e.g. array('x' => 0, 'y' => 0)
     * @param CoverTextInterface    $coverText
     * @param string                $secretItem - e.g. "100"
     */
    protected function encodeItem(array $coordinate, 
        CoverTextInterface $coverText, $secretItem
    ) {
        // get original pixel in binary
        $originalPixel = $coverText->getDecimalData($coordinate['x'], $coordinate['y']);
             
        // modified pixel could not have all chanels
        $modifiedPixel      = $originalPixel;
        $useChannel         = $this->useChannel;
        $secretItemSize     = strlen($secretItem);
        
        // encode
        for ($i = 0; $i < $secretItemSize; $i++) {
            // get channel and secret bit
            $useChannelItem  = array_shift($useChannel);
            if ($originalPixel[$useChannelItem] & 1) {
                // odd
                $modifiedPixel[$useChannelItem] = ($secretItem[$i] === '1') ? 
                    $originalPixel[$useChannelItem] : 
                    $originalPixel[$useChannelItem] - 1;  
            } else {
                // even
                $modifiedPixel[$useChannelItem] = ($secretItem[$i] === '1') ? 
                    $originalPixel[$useChannelItem] + 1 : 
                    $originalPixel[$useChannelItem]; 
            }
        }
        
        // modify pixel if it's neccesary
        $diffPixel = array_diff_assoc($originalPixel, $modifiedPixel);
        if (!empty($diffPixel)) {
            $coverText->setPixel($coordinate['x'], $coordinate['y'], $modifiedPixel);
        }
    }
    
    /**
     * Decode item
     * 
     * @param array                 $coordinate - e.g. array('x' => 0, 'y' => 0)
     * @param StegoTextInterface    $stegoText
     * @return string
     */
    protected function decodeItem(array $coordinate, 
        StegoTextInterface $stegoText    
    ) {
        $pixelData = $stegoText->getBinaryData($coordinate['x'], $coordinate['y']);
            
        $result = '';
        foreach($this->useChannel as $item) {
            $result .= substr($pixelData[$item], -1, 1);
        }
        
        return $result;
    }
}