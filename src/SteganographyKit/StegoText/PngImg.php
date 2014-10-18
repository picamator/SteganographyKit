<?php
/**
 * Stego Text - image in png format
 * 
 * PNG specification can be found here http://www.w3.org/TR/PNG-Structure.html
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoText;
use SteganographyKit\Image\PngTrait;

class PngImg extends AbstractStegoText 
{ 
    use PngTrait;
    
    /**
     * Options
     * 
     * @var array
     */
    protected $options = array(
        'path' => ''
    );
    
    /**
     * Supported Mime format
     * 
     * @var string
     */
    protected $supportedMime = 'image/png';
    
    /**
     * @param array $options
     */
    public function __construct(array $options) 
    {
        parent::__construct($options);
        
        self::validatePath($options);       
        $this->setOptions($options);
        
        $this->setImgSize($options['path']);
        $this->setImage($options['path']);
    }    
    
    /**
     * Gets converted data to binary format
     * 
     * @param integer $xIndex    x coordinat
     * @param integer $yIndex    y coordinat
     * @return array contains binary rgb representation of setting dot
     * <code>
            array('red' => ..., 'green' => ..., 'blue' => ...);
     * </code>
     * @throws Exception
     */
    public function getBinaryData($xIndex, $yIndex) 
    {        
        return $this->getBinaryColor($xIndex, $yIndex);
    }
}