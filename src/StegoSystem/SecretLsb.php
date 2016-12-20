<?php
namespace Picamator\SteganographyKit\StegoSystem;

use Picamator\SteganographyKit\Image\ImageInterface;
use Picamator\SteganographyKit\ObjectManager\ObjectManager;

/**
 * Stego System of Secret LSB
 */
class SecretLsb extends AbstractLsb
{  
    /**
     * {@inheritDoc}
     * 
     * If pixel coordinates X and Y and array of channels is ['red', 'green', 'blue'] 
     * then 'red' will have (X + Y) % 3 new index and channel 
     * that has (X + Y) % 3 will be moved to old red's place.
     *  
     * For instance X = 4, Y = 10 them (2 + 10) % 3 = 2 so we have ['blue', 'green', 'red'].
     */
    protected function getChannels($x, $y) 
    {
        $index = ($x + $y) % $this->channelsSize;
        
        $result         = $this->channels;
        $result[$index] = $this->channels[0];
        $result[0]      = $this->channels[$index];
        
        return $result;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function getImageIterator(ImageInterface $image) 
    {
        $stegoKey = $this->getStegoKey();
        $iterator = ObjectManager::getInstance()->create(
            'Picamator\SteganographyKit\Iterator\ImageRandomIterator',
            [$image, $stegoKey->getSecretKey()]
        );
        
        return $iterator;
    }
}
