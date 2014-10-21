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
        // split data accordingly channel size
        $secretData = str_split($secretData, $useChannelSize);  
        
        $imageSize          = $coverText->getImageSize();   
        $imageCoordinate    = array('x' => 0, 'y' => 0);
        $xMaxIndex          = $imageSize['width'] - 1;  
        
        // get next secret text item
        $secretItem = array_shift($secretData);
        do {   
            // encode item
            $this->encodeItem($imageCoordinate, $coverText, $secretItem);
            // move to next coordinate
            $imageCoordinate = $this->getNextImageCoordinate(
                $imageCoordinate, $xMaxIndex);           
            // move to next secret text part
            $secretItem = array_shift($secretData);
        } while (!is_null($secretItem));    
                
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
             
        // modify configured channel
        // modified pixel could not have all chanels
        $modifiedPixel      = $originalPixel;
        $useChannel         = $this->useChannel;   
        $useChannelItem     = array_shift($useChannel);
        
        $secretItem         = str_split($secretItem);
        $secretBitItem      = array_shift($secretItem);      
        do {        
            if ($originalPixel[$useChannelItem] & 1) {
                // odd
                $modifiedPixel[$useChannelItem] = ($secretBitItem === '1') ? 
                    $originalPixel[$useChannelItem] : 
                    $originalPixel[$useChannelItem] - 1;  
            } else {
                // even
                $modifiedPixel[$useChannelItem] = ($secretBitItem === '1') ? 
                    $originalPixel[$useChannelItem] + 1 : 
                    $originalPixel[$useChannelItem]; 
            }
                      
            // move to next
            $useChannelItem  = array_shift($useChannel);
            $secretBitItem   = array_shift($secretItem);
        } while (!is_null($useChannelItem) && !is_null($secretBitItem));
            
        // modify pixel if it is neccesary
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