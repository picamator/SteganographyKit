<?php
/**
 * Abstract for Least Segnificant Bit LSB (Least Segnificant Bit) Stego System
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoSystem;
use SteganographyKit\SecretText\SecretTextInterface;
use SteganographyKit\Image\ImageInterface;
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
    protected $supportedChannels = array(
        'red', 'green', 'blue'
    );
    
    /**
     * Used channels for encode - decode with a certain order
     * 
     * @var array
     */
    protected $channels;
    
    /**
     * Channels size
     * 
     * @var integer 
     */
    protected $channelsSize;
    
    /**
     * StegoKey
     * 
     * @var StegoKeyInterface 
     */
    private $stegoKey = null;
    
    public function __construct() 
    {
        // set default used channel
        $this->setChannels($this->supportedChannels);
    }
    
    /**
     * Sets channels that are going to use for encode-decode
     * 
     * @param array $channels
     * @return self
     * @throws SteganographyKit\InvalidArgumentException
     */
    public function setChannels(array $channels) 
    {
        $diff = array_diff($channels, $this->supportedChannels);
        if (!empty($diff)) {
            throw new InvalidArgumentException('Unsupported channels: ' . implode(',', $diff));
        }
        
        $this->channels     = $channels;
        $this->channelsSize = count($channels);
        
        return $this;
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
     * @param   ImageInterface      $coverText
     * @return  string
     */
    public function encode(SecretTextInterface $secretText, ImageInterface $coverText)
    {        
        // convert secret data to binary
        $secretData     = $secretText->getBinaryData(); 
        $secretSize     = strlen($secretData);    
        $iterator       = $this->getImageIterator($coverText);
        
        // validate
        $this->validateCapacity($secretSize, $coverText); 
        
        // encode
        for ($i = 0; $i <= $secretSize; $i = $i + $this->channelsSize) {
            // get item
            $secretItem = substr($secretData, $i, $this->channelsSize);
            // encode item
            $this->encodeItem($iterator, $coverText, $secretItem); 
            // move to next pixel
            $iterator->next();
        }

        // save StegoText
        return $coverText->save();
    }
    
    /**
     * Decode stegoText
     * 
     * @param   ImageInterface      $stegoText
     * @param   SecretTextInterface $secretText
     * @return  string
     */
    public function decode(ImageInterface $stegoText, SecretTextInterface $secretText)
    { 
        $iterator   = $this->getImageIterator($stegoText); 
        $result     = '';
        do {
            // get lasts bits value of pixel accordingly confugurated channel
            $result     .= $this->decodeItem($iterator, $stegoText);
            $endMarkPos  = $secretText->getEndMarkPos($result);
                             
            // move to next pixel
            $iterator->next();          
        } while ($endMarkPos === false);
        
        return $secretText->getFromBinaryData($result, $endMarkPos);
    }
        
    
    /**
     * Encode secret text item
     * 
     * @param \Iterator         $coverTextIterator
     * @param ImageInterface    $coverText
     * @param string            $secretItem - e.g. "100"
     */
    protected function encodeItem(\Iterator $coverTextIterator, 
        ImageInterface $coverText, $secretItem
    ) {  
        // get original pixel and coordinat
        $original = $coverText->getDecimalColor($coverTextIterator->current());
        list($coordinate['x'], $coordinate['y']) = explode(',', $coverTextIterator->key());
             
        // modified pixel could not have all channels
        $modified       = $original;
        $channel        = $this->getChannels($coordinate);
        $secretSize     = strlen($secretItem);
        
        // encode
        for ($i = 0; $i < $secretSize; $i++) {
            // get channel and modify bit
            $channelItem  = array_shift($channel);
            if ($original[$channelItem] & 1) {
                // odd
                $modified[$channelItem] = ($secretItem[$i] === '1') ? 
                    $original[$channelItem] : $original[$channelItem] - 1;  
            } else {
                // even
                $modified[$channelItem] = ($secretItem[$i] === '1') ? 
                    $original[$channelItem] + 1 : $original[$channelItem]; 
            }
        }
        
        // modify pixel if it's neccesary
        $diff = array_diff_assoc($original, $modified);
        if (!empty($diff)) {
            $coverText->setPixel($coordinate['x'], $coordinate['y'], $modified);
        }
    }
    
    /**
     * Decode item
     * 
     * @param \Iterator             $coverTextIterator
     * @param ImageInterface        $stegoText
     * @return string
     */
    protected function decodeItem(\Iterator $coverTextIterator, ImageInterface $stegoText) 
    {
        $pixelData = $stegoText->getBinaryColor($coverTextIterator->current());
       
        list($coordinate['x'], $coordinate['y']) = explode(',', $coverTextIterator->key());
        $channel   = $this->getChannels($coordinate);    
        
        $result = '';
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
     * @param   ImageInterface      $coverText
     * @throws  SteganographyKit\RuntimeException
     */
    abstract protected function validateCapacity($secretSize, ImageInterface $coverText);
     
    /**
     * Gets channel that should be used for current coordinate
     * It's possible that channel is choosed with dependence on coordinate
     * 
     * @param array $coordinate
     * @return array
     */
    abstract protected function getChannels(array $coordinate);
    
    /**
     * Gets image iterator
     * 
     * @param ImageInterface $image
     * @return \Iterator
     */
    abstract protected function getImageIterator(ImageInterface $image);
}