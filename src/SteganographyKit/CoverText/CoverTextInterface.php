<?php
/**
 * Interface for Cover Text (cover image)
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\CoverText;

interface CoverTextInterface 
{
    /**
     * @param array $options
     */
    public function __construct(array $options);
    
    /**
     * Gets converted data in binary format
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
     * Gets converted data in decimal format
     * 
     * @param integer $xIndex    x coordinat
     * @param integer $yIndex    y coordinat
     * @return array contains binary rgb representation of setting dot
     * <code>
            array('red' => ..., 'green' => ..., 'blue' => ...);
     * </code>
     */
    public function getDecimalData($xIndex, $yIndex);
    
    /**
     * Gets image
     * 
     * @return resource
     */
    public function getImage();
    
    /**
     * Gets how many data coverText can cover
     * 
     * @param   integer $useChannelSize
     * @return  integer
     */
    public function getCoverCapacity($useChannelSize);
    
    /**
     * Save modified image
     * 
     * @return string - path to image
     * @throws SteganographyKit\RuntimeException
     */
    public function save();
}
