<?php
/**
 * Image Trait
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace Picamator\SteganographyKit\Image;
use Picamator\SteganographyKit\Options\OptionsTrait;
use Picamator\SteganographyKit\Iterator\ImageIterator;
use Picamator\SteganographyKit\RuntimeException;
use Picamator\SteganographyKit\InvalidArgumentException;

class Image implements ImageInterface, \Countable, \IteratorAggregate 
{   
    use OptionsTrait;
    
    /**
     * Image size
     * 
     * @var array
     * <code>
     *      array(6) {
     *        'width' =>
     *         int(3264)
     *         'height' =>
     *         int(2448)
     *         'type' =>
     *         int(3)
     *         'attr' =>
     *         string(26) "width="3264" height="2448""
     *         'bits' =>
     *         int(8)
     *         'mime' =>
     *         string(9) "image/png"
     *       }
     * </code>
     */
    protected $imgSize;
    
    /**
     * Supported Type
     * 
     * @var array
     */
    protected $supportedType = array(
        IMAGETYPE_PNG,
        IMAGETYPE_JPEG,
        IMAGETYPE_GIF
    );
    
    /**
     * Link to png image resource
     * 
     * @var resource 
     */
    protected $image = null;
   
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
        $this->setOptions($options);
        $this->init();
    }
    
    /**
     * Gets iterator
     * 
     * @return ImageIterator;
     */
    public function getIterator() {
        return new ImageIterator($this);
    }
    
    /**
     * Count elements
     * 
     * @param string $mode
     * @return int The custom count as an integer
     */
    public function count($mode = 'COUNT_NORMAL') 
    {
        return $this->imgSize['width'] * $this->imgSize['height'];
    }
    
    /**
     * Gets image
     * 
     * @return resource
     */
    public function getImage() 
    {
        return $this->image;
    }
    
    /**
     * Gets image size
     * 
     * @return array
     */
    public function getSize()
    {
        return $this->imgSize;
    }        
           
    /**
     * Sets pixel
     * Modify image pixel
     * 
     * @param integer $xIndex
     * @param integer $yIndex
     * @param array $pixel
     * @return self
     * @throws SteganographyKit\RuntimeException
     */
    public function setPixel($xIndex, $yIndex, array $pixel) 
    {
        $color  = $this->getColorallocate($pixel['red'], $pixel['green'], $pixel['blue']);            

        $result = imagesetpixel($this->image, $xIndex, $yIndex, $color);
        if ($result === false) {
            throw new RuntimeException('Failed to modify pixel [' .$xIndex .', ' . $yIndex . '].' );
        }
        
        return $this;
    }
            
    /**
     * Encode color index to rgb array with binary values
     * 
     * @param integer $colorIndex result of imagecolorate
     * @return array
     * <code>
            array('red' => ..., 'green' => ..., 'blue' => ..., 'alpha' => ...);
     * </code>
     */
    public function getDecimalColor($colorIndex) 
    {   
        $result = array(
            'red'   => ($colorIndex >> 16) & 0xFF,
            'green' => ($colorIndex >> 8) & 0xFF,
            'blue'  => $colorIndex & 0xFF,
            'alpha' => ($colorIndex & 0x7F000000) >> 24
        );
//        $result = imagecolorsforindex($this->image, $colorIndex);
        
        return $result;
    }
    
    /**
     * Encode decimalPixel to binary
     * 
     * @param integer $colorIndex result of imagecolorate
     * @return array
     * <code>
            array('red' => ..., 'green' => ..., 'blue' => ..., 'alpha' => ...);
     * </code>
     */
    public function getBinaryColor($colorIndex)
    {   
        $result = $this->getDecimalColor($colorIndex);
        foreach($result as &$item) {
            $item = sprintf('%08d', decbin($item));
        }
        unset($item);
        
        return $result;
    } 
    
    /**
     * Save image
     * 
     * @return boolean true if ok or false otherwise
     */
    public function save() 
    {
        if (empty($this->options['savePath']) || 
            imagepng($this->image, $this->options['savePath']) === false    
        ) {
            return false;
        }
        
        return true;
    } 
    
    /**
     * Destroy image
     */
    public function __destruct()
    {
       if (!is_null($this->image)) {
            imagedestroy($this->image);
        }
    }
       
    /**
     * Sets Image
     * 
     * @param string $path
     * @throws SteganographyKit\InvalidArgumentException
     */
    protected function setImage($path) 
    {
        switch ($this->imgSize['type']) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($path);
                break;
            
            case IMAGETYPE_GIF;
                $image = imagecreatefromgif($path);
                break;

            case IMAGETYPE_PNG;
                $image = imagecreatefrompng($path);
                break;
            
            default:
                $image = false;
        }
        
        if($image === false) {
            throw new InvalidArgumentException('Can not create image by path: ' . $path);
        }
        
        $this->image = $image;
    }
    
    /**
     * Gets Colorallocate
     * It works only for truecolor otherwise it should be used imagecolorallocate
     * 
     * @param integer $red      0-255
     * @param integer $green    0-255
     * @param integer $blue     0-255
     * @return integer
     */
    protected function getColorallocate($red, $green, $blue)
    {
        $result = ($red << 16) | ($green << 8) | $blue;
//        $result = imagecolorallocate($this->image, $red, $green, $blue)
        
        return $result;
    }
     
    /**
     * Sets image size
     * 
     * @param string $path
     * @throws SteganographyKit\RuntimeException
     */
    protected function setImgSize($path) 
    {
        $result = getimagesize($path);
        if($result === false) {
            throw new RuntimeException('Imposible to calculate image size: ' . $path);
        }
        
        $this->imgSize = array_combine(
            array('width', 'height', 'type', 'attr', 'bits', 'mime'),
            $result     
        );
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
    
    /**
     * Verify is GB Lib extension was loaded
     * 
     * @throws SteganographyKit\RuntimeException
     */
    protected function validateGbLib() 
    {
        if(extension_loaded('gd') === false) {
            throw new RuntimeException('GD php extension was not loaded: http://www.php.net/manual/en/book.image.php');
        } 
    }
    
    /**
     * Validate path
     * 
     * @param string $path
     * @throws SteganographyKit\InvalidArgumentException
     */
    protected function validatePath($path)
    {
        if(!file_exists($path) || !is_readable($path)) {    
            throw new InvalidArgumentException('Incorrect path "' . $path . '". Image does not exsit or not readable.');
        }
    }
    
    /**
     * Validate savePath
     * 
     * @param string $savePath
     * @throws SteganographyKit\InvalidArgumentException
     */
    protected function validateSavePath($savePath) 
    {
        if (empty($savePath)) {
            return;
        }
        
        $dirPath = dirname($savePath);      
        if(!file_exists($dirPath) && !mkdir($dirPath, 0755, true)) {
            throw new InvalidArgumentException('Impossible create subfolders structure for destination: ' . $savePath);
        } else if(!is_writable($dirPath)) {
            throw new InvalidArgumentException('Destination does not have writable permission: ' . $dirPath);
        }
    }
    
    /**
     * Validate supported types
     * 
     * @throws SteganographyKit\InvalidArgumentException
     */
    protected function validateType() 
    {
        if (!in_array($this->imgSize['type'], $this->supportedType)) {
            throw new InvalidArgumentException('Image with type: "' . $this->imgSize['type'] 
                . '", mime: "' . $this->imgSize['mime'] . '" is not supported.'
            );
        }
    }
}