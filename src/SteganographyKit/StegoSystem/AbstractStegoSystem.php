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
     * Validate is it enouph room into coverText to keep secret one
     * 
     * @param   SecretTextInterface $secretText
     * @param   CoverTextInterface $coverText
     * @throws  Exception
     */
     protected function validateEncode(SecretTextInterface $secretText, 
        CoverTextInterface $coverText
    ) {
         $secretSize = $secretText->getSize();
         $coverSize  = $coverText->getCoverSize();        
         if ($secretSize > $coverSize) {
             throw new Exception('Not enouph room to keep all secretText. CoverText can handle '
                . $coverSize . ' bytes but SecretTest has ' . $secretSize . ' bytes');
         }
    }
}