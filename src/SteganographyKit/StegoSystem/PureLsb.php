<?php
/**
 * Stego System of Pure LSB
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoSystem;
use SteganographyKit\CoverText\CoverTextInterface;
use SteganographyKit\RuntimeException;

class PureLsb extends AbstractLsb
{    
    /**
     * {@inheritDoc}
     */
    protected function getNextCoordinate(array $prevCoordinate, $xMax, $yMax) 
    {
        $prevCoordinate['x']++;
        if ($prevCoordinate['x'] > $xMax) {
            $prevCoordinate['x'] = 0;
            $prevCoordinate['y']++;
        } 
        
        if ($prevCoordinate['y'] > $yMax) {
            return false;
        }
        
        return $prevCoordinate;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function validateCapacity($secretSize, CoverTextInterface $coverText) 
    {
         $coverCapacity  = $coverText->getCoverCapacity($this->useChannelSize);        
         if ($secretSize > $coverCapacity) {
             throw new RuntimeException('Not enouph room to keep all secretText. CoverText can handle '
                . $coverCapacity . ' bytes but SecretTest has ' . $secretSize . ' bytes');
         }
    }
    
    /**
     * {@inheritDoc}
     */
    protected function getChannel(array $coordinate) 
    {
        return $this->useChannel;
    }
}