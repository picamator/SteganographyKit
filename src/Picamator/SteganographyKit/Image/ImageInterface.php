<?php
/**
 * Interface for Image
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace Picamator\SteganographyKit\Image;

interface ImageInterface
{
    /**
     * Gets image
     * 
     * @return resource
     */
    public function getImage();
    
    /**
     * Gets image size
     * 
     * @return array
     */
    public function getSize();      
            
    /**
     * Sets pixel
     * Modify image pixel
     * 
     * @param integer $xIndex
     * @param integer $yIndex
     * @param array $pixel
     * @return self
     * @throws RuntimeException
     */
    public function setPixel($xIndex, $yIndex, array $pixel);
    
    /**
     * Encode color index to rbb array with binary values
     * 
     * @param integer $colorIndex result of imagecolorate
     * @return array
     * <code>
            array('red' => ..., 'green' => ..., 'blue' => ..., 'alpha' => ...);
     * </code>
     */
    public function getDecimalColor($colorIndex);
    
    /**
     * Encode decimalPixel to binary
     * 
     * @param integer $colorIndex result of imagecolorate
     * @return array
     * <code>
            array('red' => ..., 'green' => ..., 'blue' => ..., 'alpha' => ...);
     * </code>
     */
    public function getBinaryColor($colorIndex);
    
    /**
     * Save image
     * 
     * @return boolean true if ok or false otherwise
     */
    public function save();
    
}
