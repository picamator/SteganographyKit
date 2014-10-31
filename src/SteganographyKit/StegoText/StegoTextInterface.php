<?php
/**
 * Interface for Stego Text (cover text woth embeded secret text)
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoText;

interface StegoTextInterface 
{
    /**
     * @param array $options
     */
    public function __construct(array $options);
    
    /**
     * Gets converted data to binary format
     * 
     * @param integer $xIndex    x coordinat
     * @param integer $yIndex    y coordinat
     * @return array contains binary rgb representation of setting dot
     * <code>
            array('red' => ..., 'green' => ..., 'blue' => ...);
     * </code>
     */
    public function getBinaryData($xIndex, $yIndex);
    
    /**
     * Gets image
     * 
     * @return resource
     */
    public function getImage();
}
