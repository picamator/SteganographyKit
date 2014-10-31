<?php
/**
 * Interface for Stego System
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoSystem;
use SteganographyKit\SecretText\SecretTextInterface;
use SteganographyKit\StegoText\StegoTextInterface;
use SteganographyKit\CoverText\CoverTextInterface;
use SteganographyKit\StegoKey\StegoKeyInterface;

interface StegoSystemInterface 
{
    /**
     * Sets stegoKey
     * 
     * @param StegoKeyInterface $stegoKey
     * @return self
     */
    public function setStegoKey(StegoKeyInterface $stegoKey);
    
    /**
     * Sets channels that are going to use for encode-decode
     * 
     * @param array $useChannel
     * @return self
     * @throws SteganographyKit\InvalidArgumentException
     */
    public function setUseChannel(array $useChannel);
    
    /**
     * Gets supported channels
     * 
     * @return array
     */
    public function getSupportedChannel();
    
    /**
     * Encode secretText
     * 
     * @param   SecretTextInterface $secretText
     * @param   CoverTextInterface  $coverText
     * @return  string
     */
    public function encode(SecretTextInterface $secretText, 
        CoverTextInterface $coverText);
    
    /**
     * Decode stegoText
     * 
     * @param   StegoTextInterface  $stegoText
     * @param   SecretTextInterface $secretText
     * @return  string
     */
    public function decode(StegoTextInterface $stegoText, 
        SecretTextInterface $secretText);
}
