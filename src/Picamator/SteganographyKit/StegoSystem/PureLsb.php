<?php
/**
 * Stego System of Pure LSB
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace Picamator\SteganographyKit\StegoSystem;
use Picamator\SteganographyKit\Image\ImageInterface;

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
