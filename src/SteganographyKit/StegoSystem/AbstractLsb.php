<?php
/**
 * Abstract for Least Segnificant Bit LSB (Least Segnificant Bit) Stego System
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoSystem;
use SteganographyKit\SecretText\SecretTextInterface;
use SteganographyKit\StegoText\StegoTextInterface;
use SteganographyKit\CoverText\CoverTextInterface;
use SteganographyKit\StegoKey\StegoKeyInterface;
use SteganographyKit\InvalidArgumentException;
use SteganographyKit\LogicException;

abstract class AbstractLsb implements StegoSystemInterface 
{
    /**
     * List of supported channels
     * That can be used by stegoSystem
     * 
     * @var array
     */
    protected $supportedChannel = array(
        'red', 'green', 'blue'
    );
    
    /**
     * Used channels for encode - decode with a certain order
     * 
     * @var array
     */
    protected $useChannel;
    
    /**
     * Size of channels that is used
     * 
     * @var integer 
     */
    protected $useChannelSize;
    
    /**
     * StegoKey
     * 
     * @var StegoKeyInterface 
     */
    private $stegoKey = null;
    
    public function __construct() 
    {
        // set default used channel
        $this->setUseChannel($this->supportedChannel);
    }
    
    /**
     * Sets channels that are going to use for encode-decode
     * 
     * @param array $useChannel
     * @return self
     * @throws SteganographyKit\InvalidArgumentException
     */
    public function setUseChannel(array $useChannel) 
    {
        $diffChannel = array_diff($useChannel, $this->supportedChannel);
        if (!empty($diffChannel)) {
            throw new InvalidArgumentException('Unsupported channels: ' . implode(',', $diffChannel));
        }
        
        $this->useChannel       = $useChannel;
        $this->useChannelSize   = count($useChannel);
        
        return $this;
    }
    
    /**
     * Gets supported channels
     * 
     * @return array
     */
    public function getSupportedChannel() 
    {
        return $this->supportedChannel;
    }
    
    /**
     * Sets stegoKey
     * 
     * @param StegoKeyInterface $stegoKey
     * @return self
     */
    public function setStegoKey(StegoKeyInterface $stegoKey) 
    {
        $this->stegoKey = $stegoKey;
        
        return $this;
    }
    
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
        // convert secret data to binary
        $secretData = $secretText->getBinaryData(); 
        
        // validate
        $secretDataSize = strlen($secretData);
        $this->validateCapacity($secretDataSize, $coverText);
        
        $imageSize          = $coverText->getImageSize();   
        $coordinate         = array('x' => 0, 'y' => 0);
        $xMax               = $imageSize['width'] - 1;
        $yMax               = $imageSize['height'] - 1;  
        
        // encode
        for ($i = 0; $i <= $secretDataSize; $i = $i + $this->useChannelSize) {
            // get item
            $secretItem = substr($secretData, $i, $this->useChannelSize);
            // encode item
            $this->encodeItem($coordinate, $coverText, $secretItem);
            // move to next coordinate
            $coordinate = $this->getNextCoordinate($coordinate, $xMax, $yMax);        
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
        $imgSize        = $stegoText->getImageSize(); 
        $coordinate     = array('x' => 0, 'y' => 0);
        $xMax           = $imgSize['width'] - 1;   
        $yMax           = $imgSize['height'] - 1;   
        $result         = '';        
        do {
            // get lasts bits value of pixel accordingly confugurated channel
            $result     .= $this->decodeItem($coordinate, $stegoText);
            $endMarkPos  = $secretText->getEndMarkPos($result);
                             
            // get next pixel
            $coordinate = $this->getNextCoordinate($coordinate, $xMax, $yMax);           
        } while ($endMarkPos === false && $coordinate !== false);
        
        return $secretText->getFromBinaryData($result, $endMarkPos);
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
             
        // modified pixel could not have all channels
        $modifiedPixel      = $originalPixel;
        $channel            = $this->getChannel($coordinate);
        $secretItemSize     = strlen($secretItem);
        
        // encode
        for ($i = 0; $i < $secretItemSize; $i++) {
            // get channel and modify bit
            $channelItem  = array_shift($channel);
            if ($originalPixel[$channelItem] & 1) {
                // odd
                $modifiedPixel[$channelItem] = ($secretItem[$i] === '1') ? 
                    $originalPixel[$channelItem] : $originalPixel[$channelItem] - 1;  
            } else {
                // even
                $modifiedPixel[$channelItem] = ($secretItem[$i] === '1') ? 
                    $originalPixel[$channelItem] + 1 : $originalPixel[$channelItem]; 
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
    protected function decodeItem(array $coordinate, StegoTextInterface $stegoText) 
    {
        $pixelData = $stegoText->getBinaryData($coordinate['x'], $coordinate['y']);
        $channel   = $this->getChannel($coordinate);    
        
        $result     = '';
        foreach($channel as $item) {
            $result .= substr($pixelData[$item], -1, 1);
        }
        
        return $result;
    }
     
    /**
     * Gets stegoKey
     * 
     * @return StegoKeyInterface
     * @throws SteganographyKit\LogicException
     */
    protected function getStegoKey() 
    {
        if (is_null($this->stegoKey)) {
            throw new LogicException('StegoKey was not set');
        }
        
        return $this->stegoKey;
    }
    
    /**
     * Validate is it enouph room into coverText to keep secret one
     * 
     * @param   integer             $secretSize
     * @param   CoverTextInterface  $coverText
     * @throws  SteganographyKit\RuntimeException
     */
    abstract protected function validateCapacity($secretSize, CoverTextInterface $coverText);
    
    /**
     * Gets next image coordinate
     * 
     * @param array     $prevCoordinate - previous coordinate
     * @param integer   $xMax
     * @param integer   $yMax
     * @return array|false - array with next coordinate or false if out of bondary
     */
    abstract protected function getNextCoordinate(array $prevCoordinate, $xMax, $yMax);
    
    /**
     * Gets channel that should be used for current coordinate
     * It's possible that channel is choosed with dependence on coordinate
     * 
     * @param array $coordinate
     * @return array
     */
    abstract protected function getChannel(array $coordinate);
}