<?php
/**
 * Cover Text - image in png format
 * 
 * PNG specification can be found here http://www.w3.org/TR/PNG-Structure.html
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\CoverText;
use SteganographyKit\Image\PngTrait;

class PngImg extends AbstractCoverText 
{ 
    use PngTrait;
    
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
        
        $this->validatePath($options);
        $this->validateSavePath($options);
        $this->setOptions($options);
        
        $this->setImgSize($options['path']);
        $this->validateMime();
        $this->setImage($options['path']);
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    public function save() 
    {
        if(!imagepng($this->image, $this->options['savePath'])) {
            throw new Exception('Can not save result image to destination '
                . $this->options['savePath']);
        }
        
        return $this->options['savePath'];
    }
}