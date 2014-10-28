<?php
/**
 * Stego System of Secret LSB
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoSystem;
use SteganographyKit\SecretText\SecretTextInterface;
use SteganographyKit\CoverText\CoverTextInterface;

class SecretLsb extends AbstractLsb
{    
    /**
     * {@inheritDoc}
     */
    protected function getNextCoordinate(array $prevCoordinate, $xMax, $yMax) 
    {
        $result = $this->getStegoKey()
            ->getCoordinate($xMax, $yMax);
        
        return $result;
    }
    
    /**
     * {@inheritDoc} 4 time less then in PureLsb
     */
    protected function validateCapacity(SecretTextInterface $secretText, 
        CoverTextInterface $coverText
    ) {
         $secretSize     = $secretText->getSize();
         $coverCapacity  = $coverText->getCoverCapacity($this->useChannelSize) / 4;        
         if ($secretSize > $coverCapacity) {
             throw new Exception('Not enouph room to keep all secretText. CoverText can handle '
                . $coverCapacity . ' bytes but SecretTest has ' . $secretSize . ' bytes');
         }
    }
    
    /**
     * {@inheritDoc}
     * 
     * If pixel coordinates X and Y and array of channels is ['red', 'green', 'blue'] 
     * then 'red' will have (X + Y) % 3 new index and channel 
     * that has (X + Y) % 3 will be moved to old red's place.
     *  
     * For instance X = 4, Y = 10 them (2 + 10) % 3 = 2 so we have ['blue', 'green', 'red'].
     */
    protected function getChannel(array $coordinate) 
    {
        $index          = array_sum($coordinate) % $this->useChannelSize;
        
        $result         = $this->useChannel;
        $result[$index] = $this->useChannel[0];
        $result[0]      = $this->useChannel[$index];
        
        return $this->useChannel;
    }
}
