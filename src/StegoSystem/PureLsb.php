<?php
namespace Picamator\SteganographyKit\StegoSystem;

use Picamator\SteganographyKit\Image\ImageInterface;

/**
 * Stego System of Pure LSB
 */
class PureLsb extends AbstractLsb
{   
    /**
     * {@inheritDoc}
     */
    protected function getChannels($x, $y) 
    {
        return $this->channels;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function getImageIterator(ImageInterface $image) 
    {
        return $image->getIterator();
    }
}
