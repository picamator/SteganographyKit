<?php
/**
 * Stego System of Pure LSB
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoSystem;
use SteganographyKit\Image\ImageInterface;
use SteganographyKit\RuntimeException;

class PureLsb extends AbstractLsb
{
    /**
     * {@inheritDoc}
     */
    protected function validateCapacity($secretSize, ImageInterface $coverText) 
    {
        $imgSize        = $coverText->getSize();
        $coverCapacity  = $this->channelsSize * $imgSize['width'] * $imgSize['height'];    
        
        if ($secretSize > $coverCapacity) {
            throw new RuntimeException('Not enouph room to keep all secretText. CoverText can handle '
               . $coverCapacity . ' bytes but SecretTest has ' . $secretSize . ' bytes');
        }
    }
    
    /**
     * {@inheritDoc}
     */
    protected function getChannels(array $coordinate) 
    {
        return $this->channels;
    }
    
    protected function getImageIterator(ImageInterface $image) 
    {
        return $image->getIterator();
    }
}