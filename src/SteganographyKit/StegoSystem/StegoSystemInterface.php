<?php
/**
 * Interface for Stego System
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoSystem;
use SteganographyKit\SecretText\SecretTextInterface;
use SteganographyKit\Image\ImageInterface;
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
     * @param array $channels
     * @return self
     * @throws SteganographyKit\InvalidArgumentException
     */
    public function setChannels(array $channels);
        
    /**
     * Encode secretText
     * 
     * @param   SecretTextInterface $secretText
     * @param   ImageInterface      $coverText
     * @return  string
     */
    public function encode(SecretTextInterface $secretText, ImageInterface $coverText);
    
    /**
     * Decode stegoText
     * 
     * @param   ImageInterface      $stegoText
     * @param   SecretTextInterface $secretText
     * @return  string
     */
    public function decode(ImageInterface $stegoText, SecretTextInterface $secretText);
}
