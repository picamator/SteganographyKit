<?php
/**
 * Cover Text
 * Image is saved in png format
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\CoverText;
use SteganographyKit\Image\ImageTrait;
use SteganographyKit\RuntimeException;

class CoverImage extends AbstractCoverText 
{ 
    use ImageTrait;
    
    /**
     * Options
     * 
     * @var array
     */
    protected $options = array(
        'path'      => '',
        'savePath'  => ''
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
     * Gets converted data in binary format
     * 
     * @param integer $xIndex    x coordinat
     * @param integer $yIndex    y coordinat
     * @return array contains binary rgb representation of setting dot
     * <code>
            array('red' => ..., 'green' => ..., 'blue' => ..., 'alpha' => ...,);
     * </code>
     */
    public function getBinaryData($xIndex, $yIndex) 
    {        
        return $this->getBinaryColor($xIndex, $yIndex);
    }
    
    /**
     * Gets converted data in decimal format
     * 
     * @param integer $xIndex    x coordinat
     * @param integer $yIndex    y coordinat
     * @return array contains binary rgb representation of setting dot
     * <code>
            array('red' => ..., 'green' => ..., 'blue' => ..., 'alpha' => ...);
     * </code>
     */
    public function getDecimalData($xIndex, $yIndex) 
    {        
        return $this->getDecimalColor($xIndex, $yIndex);
    }
    
    /**
     * Gets how many data coverText can cover
     * 
     * @param  integer $useChannelSize
     * @return integer
     */
    public function getCoverCapacity($useChannelSize) 
    {
        return $this->imgSize['width'] * $this->imgSize['height'] * $useChannelSize;
    }
    
    /**
     * Save modified image
     * 
     * @return string - path to image
     * @throws SteganographyKit\RuntimeException
     */
    public function save() 
    {
        if(!imagepng($this->image, $this->options['savePath'])) {
            throw new RuntimeException('Can not save result image to destination: '
                . $this->options['savePath']);
        }
        
        return $this->options['savePath'];
    }
    
    /**
     * Initialize and validation
     */
    protected function init() 
    {
        $this->validateGbLib();
        $this->validatePath($this->options['path']);
        $this->validateSavePath($this->options['savePath']);
        
        $this->setImgSize($this->options['path']);
        $this->validateType();
        
        $this->setImage($this->options['path']);
    }
}