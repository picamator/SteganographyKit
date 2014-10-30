<?php
/**
 * Stego Text - image
 * 
 * PNG specification can be found here http://www.w3.org/TR/PNG-Structure.html
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoText;
use SteganographyKit\Image\ImageTrait;

class StegoImage extends AbstractStegoText 
{ 
    use ImageTrait;
    
    /**
     * Options
     * 
     * @var array
     */
    protected $options = array(
        'path' => ''
    );
    
    /**
     * @param array $options
     */
    public function __construct(array $options) 
    {
        parent::__construct($options);
        
        $this->init();
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
    
    /**
     * Initialize and validation
     */
    protected function init() 
    {        
        $this->validateGbLib();
        $this->validatePath($this->options['path']);
        
        $this->setImgSize($this->options['path']);
        $this->validateType();
        
        $this->setImage($this->options['path']);
    }
}