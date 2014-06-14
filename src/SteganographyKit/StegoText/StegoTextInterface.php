<?php
/**
 * Interface for Stego Text (cover image)
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
     * @param integer $xIndex
     * @param integer $yIndex
     * @return array contains binary rgb representation of image
     * <code>
     *  array(
     *      0 => array(0 => array('red' => ..., 'green' => ..., 'blue' => ...)),
     *      ...
     *  );
     * </code>
     */
    public function getBinaryData($xIndex = null, $yIndex = nul);
    
    /**
     * Gets size of suppose Embedded data
     * 
     * @return integer
     */
    public function getSecretTextSize();
}
