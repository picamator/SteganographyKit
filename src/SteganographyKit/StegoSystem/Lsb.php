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
        $imageCoordinate    = array('x' => 0, 'y' => 0);
        $xMaxIndex          = $imageSize['width'] - 1;
        $secretDataSize     = strlen($secretData);
        
        // encode
        for ($i = 0; $i <= $secretDataSize; $i = $i + $useChannelSize) {
            // get item
            $secretItem = substr($secretData, $i, $useChannelSize);
            // encode item
            $this->encodeItem($imageCoordinate, $coverText, $secretItem);
            // move to next coordinate
            $imageCoordinate = $this->getNextImageCoordinate(
                $imageCoordinate, $xMaxIndex);        
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
        $imageSize          = $stegoText->getImageSize(); 
        $imageCoordinate    = array('x' => 0, 'y' => 0);
        $xMaxIndex          = $imageSize['width'] - 1;   
        $yMaxIndex          = $imageSize['height'] - 1;   
        $result             = '';        
        do {
            // get lasts bits value of pixel accordingly confugurated channel
            $result .= $this->decodeItem($imageCoordinate, $stegoText);
            $endMarkPos  = $secretText->getEndMarkPos($result);
                             
            // get next pixel
            $imageCoordinate = $this->getNextImageCoordinate($imageCoordinate, $xMaxIndex);           
        } while ($endMarkPos === false
            && ($imageCoordinate['x'] !== $xMaxIndex || $imageCoordinate['y'] !== $yMaxIndex)
        );
        
        // handle last pixel
        if($endMarkPos === false) {
            $result     .= $this->decodeItem($imageCoordinate, $stegoText);
            $endMarkPos  = $secretText->getEndMarkPos($result);
        }
        
        return $secretText->getFromBinaryData($result, $endMarkPos);
    }
    
    /**
     * Gets next image coordinate
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
     * @param string                $secretItem - e.g. "100"
     */
    protected function encodeItem(array $imageCoordinate, 
        CoverTextInterface $coverText, $secretItem
    ) {         
        // get original pixel in binary
        $originalPixel = $coverText->getDecimalData(
            $imageCoordinate['x'], $imageCoordinate['y']
        );
             
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
            // apply modification
            $coverText->setPixel($imageCoordinate['x'], $imageCoordinate['y'], $modifiedPixel);
        }
    }
    
    /**
     * Decode item
     * 
     * @param array                 $imageCoordinate - e.g. array('x' => 0, 'y' => 0)
     * @param StegoTextInterface    $stegoText
     * @return string
     */
    protected function decodeItem(array $imageCoordinate, StegoTextInterface $stegoText) {
        $pixelData = $stegoText->getBinaryData(
            $imageCoordinate['x'], 
            $imageCoordinate['y']
        );
            
        $result = '';
        foreach($this->useChannel as $item) {
            $result .= substr($pixelData[$item], -1, 1);
        }
        
        return $result;
    }
}