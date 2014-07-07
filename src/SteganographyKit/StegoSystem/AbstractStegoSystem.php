<?php
/**
 * Abstract for Stego System
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoSystem;
use SteganographyKit\SecretText\SecretTextInterface;
use SteganographyKit\StegoText\StegoTextInterface;
use SteganographyKit\CoverText\CoverTextInterface;

abstract class AbstractStegoSystem implements StegoSystemInterface 
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
     * Used channels for encode - decode
     * with a certain order
     * 
     * 
     * @var array
     */
    protected $useChannel;
    
    /**
     * Sets channels that are going to use for encode-decode
     * 
     * @param array $useChannel
     * @return self
     * @throws Exception
     */
    public function setUseChannel(array $useChannel) 
    {
        $validate = array_diff($this->supportedChannel, $useChannel);
        if (!empty($validate)) {
            throw new Exception('Unsupported channels: ' . implode(',', $validate));
        }
        
        $this->useChannel = $useChannel;
    }
    
    /**
     * Validate is it enouph room into coverText to keep secret one
     * 
     * @param   SecretTextInterface $secretText
     * @param   CoverTextInterface  $coverText
     * @param   Integer             $useChannelSize - how many channels is used
     * @throws  Exception
     */
     protected function validateEncode(SecretTextInterface $secretText, 
        CoverTextInterface $coverText, $useChannelSize
    ) {
         $secretSize     = $secretText->getSize();
         $coverCapacity  = $coverText->getCoverCapacity($useChannelSize);        
         if ($secretSize > $coverCapacity) {
             throw new Exception('Not enouph room to keep all secretText. CoverText can handle '
                . $coverCapacity . ' bytes but SecretTest has ' . $secretSize . ' bytes');
         }
    }
}