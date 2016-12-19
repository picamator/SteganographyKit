<?php
/**
 * Abstract for Least Segnificant Bit LSB (Least Segnificant Bit) Stego System
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace Picamator\SteganographyKit\StegoSystem;
use Picamator\SteganographyKit\SecretText\SecretTextInterface;
use Picamator\SteganographyKit\Image\ImageInterface;
use Picamator\SteganographyKit\StegoKey\StegoKeyInterface;
use Picamator\SteganographyKit\InvalidArgumentException;
use Picamator\SteganographyKit\LogicException;
use Picamator\SteganographyKit\RuntimeException;

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
     * @throws InvalidArgumentException
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
        $this->validateCapacity($secretText, $coverText);
        $secretText->setBinaryItemSize($this->channelsSize);
        
        $iterator = new \MultipleIterator(\MultipleIterator::MIT_NEED_ALL|\MultipleIterator::MIT_KEYS_ASSOC);
        $iterator->attachIterator($this->getImageIterator($coverText), 'img');
        $iterator->attachIterator($secretText->getIterator(), 'secText');
        
        foreach ($iterator as $item) {
            $this->encodeItem($item['img'], $coverText, $item['secText']);
        }

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
        $secretText->setBinaryItemSize($this->channelsSize);
        $iterator   = $this->getImageIterator($stegoText); 
        $result     = '';
        do {
            // get lasts bits value of pixel accordingly confugurated channel
            $result .= $this->decodeItem($iterator, $stegoText);
            // move to next pixel
            $iterator->next();          
        } while ($secretText->getEndMarkPos($result) === false);
                    
        return $secretText->getFromBinaryData($result);
    }
         
    /**
     * Encode secret text item
     * 
     * @param array             $pixelData coordinate with color ex. ['x' => 0, 'y' => 0, 'color' => 732327]
     * @param ImageInterface    $coverText
     * @param string            $secretItem - e.g. "100"
     */
    protected function encodeItem(array $pixelData, 
        ImageInterface $coverText, $secretItem
    ) {  
        $colorData      = $coverText->getDecimalColor($pixelData['color']);
        $channels       = $this->getChannels($pixelData['x'], $pixelData['y']);
        $secretSize     = strlen($secretItem);
        
        // encode
        for ($i = 0; $i < $secretSize; $i++) {
            // get channel and modify bit
            $channelItem  = array_shift($channels);
            if ($colorData[$channelItem] & 1) {
                // odd
                $colorData[$channelItem] = ($secretItem[$i] === '1') ? 
                    $colorData[$channelItem] : $colorData[$channelItem] - 1;  
            } else {
                // even
                $colorData[$channelItem] = ($secretItem[$i] === '1') ? 
                    $colorData[$channelItem] + 1 : $colorData[$channelItem]; 
            }
        }
        
        $coverText->setPixel($pixelData['x'], $pixelData['y'], $colorData);
    }
    
    /**
     * Decode item
     * 
     * @param \Iterator         $coverTextIterator
     * @param ImageInterface    $stegoText
     * @return string
     */
    protected function decodeItem(\Iterator $coverTextIterator, ImageInterface $stegoText) 
    {
        $pixelData  = $coverTextIterator->current();       
        $colorRgb   = $stegoText->getBinaryColor($pixelData['color']);     
        $channels   = $this->getChannels($pixelData['x'], $pixelData['y']);    
               
        $result = '';
        foreach($channels as $item) {
            $result .= substr($colorRgb[$item], -1, 1);
        }
               
        return $result;
    }
     
    /**
     * Gets stegoKey
     * 
     * @return StegoKeyInterface
     * @throws LogicException
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
     * @param   SecretTextInterface $secretText
     * @param   ImageInterface      $coverText
     * @throws  RuntimeException
     */
    protected function validateCapacity(SecretTextInterface $secretText, ImageInterface $coverText)
    {
        $coverCapacity  = $this->channelsSize * count($coverText);    
        $secretSize     = count($secretText);
        
        if ($secretSize > $coverCapacity) {
            throw new RuntimeException('Not enouph room to keep all secretText. CoverText can handle '
               . $coverCapacity . ' bytes but SecretTest has ' . $secretSize . ' bytes');
        }
    }
     
    /**
     * Gets channel that should be used for current coordinate
     * It's possible that channel is choosed with dependence on coordinate
     * 
     * @param integer $x
     * @param integer $y
     * @return array
     */
    abstract protected function getChannels($x, $y);
    
    /**
     * Gets image iterator
     * 
     * @param ImageInterface $image
     * @return \Iterator
     */
    abstract protected function getImageIterator(ImageInterface $image);
}
