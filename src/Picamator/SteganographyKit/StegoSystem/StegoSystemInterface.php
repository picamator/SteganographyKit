<?php
/**
 * Interface for Stego System
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace Picamator\SteganographyKit\StegoSystem;
use Picamator\SteganographyKit\SecretText\SecretTextInterface;
use Picamator\SteganographyKit\Image\ImageInterface;
use Picamator\SteganographyKit\StegoKey\StegoKeyInterface;

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
     * @throws InvalidArgumentException
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
