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
        $this->validateEncode($secretText, $coverText);
        
        // calculate max value of caverText coordinats
        $imgSize = $coverText->getImageSize();
    }
    
    /**
     * Decode stegoText
     * 
     * @param   StegoTextInterface $stegoText
     * @return  string
     */
    public function decode(StegoTextInterface $stegoText) 
    {
        // @TODO
    }
}