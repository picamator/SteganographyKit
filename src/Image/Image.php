<?php
namespace Picamator\SteganographyKit\Image;

use Picamator\SteganographyKit\ObjectManager\ObjectManager;
use Picamator\SteganographyKit\Options\OptionsTrait;
use Picamator\SteganographyKit\Iterator\ImageIterator;
use Picamator\SteganographyKit\RuntimeException;
use Picamator\SteganographyKit\InvalidArgumentException;

/**
 * Image
 */
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
    protected $supportedType = [
        IMAGETYPE_PNG,
        IMAGETYPE_JPEG,
        IMAGETYPE_GIF,
    ];
    
    /**
     * Link to png image resource
     * 
     * @var resource 
     */
    protected $image = null;
   
    /**
     * Default Options
     * 
     * @var array
     */
    protected $optionsDefault = [
        'path'      => '',
        'savePath'  => '',
    ];

    /**
     * @var ImageIterator | null
     */
    private $iterator = null;

    /**
     * @param array $options
     */
    public function __construct(array $options) 
    {
        $this->setOptions($options, $this->optionsDefault);
        $this->init();
    }
    
    /**
     * Gets iterator
     * 
     * @return ImageIterator;
     */
    public function getIterator()
    {
        if (is_null($this->iterator)) {
            $this->iterator = ObjectManager::getInstance()->create('Picamator\SteganographyKit\Iterator\ImageIterator', [$this]);
        }

        return $this->iterator;
    }
    
    /**
     * Count elements
     * 
     * @param string $mode
     *
     * @return int The custom count as an int
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
     * @param int $xIndex
     * @param int $yIndex
     * @param array $pixel
     *
     * @return self
     *
     * @throws RuntimeException
     */
    public function setPixel($xIndex, $yIndex, array $pixel) 
    {
        $color  = $this->getColorallocate($pixel['red'], $pixel['green'], $pixel['blue']);            

        $result = imagesetpixel($this->image, $xIndex, $yIndex, $color);
        if ($result === false) {
            throw new RuntimeException(
                sprintf('Failed to modify pixel [%s, %s].', $xIndex, $yIndex)
            );
        }
        
        return $this;
    }
            
    /**
     * Encode color index to rgb array with binary values
     * 
     * @param int $colorIndex result of imagecolorate
     *
     * @return array
     * <code>
            array('red' => ..., 'green' => ..., 'blue' => ..., 'alpha' => ...);
     * </code>
     */
    public function getDecimalColor($colorIndex) 
    {   
        $result = [
            'red'   => ($colorIndex >> 16) & 0xFF,
            'green' => ($colorIndex >> 8) & 0xFF,
            'blue'  => $colorIndex & 0xFF,
            'alpha' => ($colorIndex & 0x7F000000) >> 24
        ];
        
        return $result;
    }
    
    /**
     * Encode decimalPixel to binary
     * 
     * @param int $colorIndex result of imagecolorate
     *
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
     * @return bool true if ok or false otherwise
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
     * @throws InvalidArgumentException
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
            throw new InvalidArgumentException(
                sprintf('Can not create image by path "%s"', $path)
            );
        }
        
        $this->image = $image;
    }
    
    /**
     * Gets Colorallocate
     * It works only for truecolor otherwise it should be used imagecolorallocate
     * 
     * @param int $red      0-255
     * @param int $green    0-255
     * @param int $blue     0-255
     *
     * @return int
     */
    protected function getColorallocate($red, $green, $blue)
    {
        $result = ($red << 16) | ($green << 8) | $blue;
        
        return $result;
    }
     
    /**
     * Sets image size
     * 
     * @param string $path
     * @throws RuntimeException
     */
    protected function setImgSize($path) 
    {
        $result = getimagesize($path);
        if($result === false) {
            throw new RuntimeException(
                sprintf('Impossible calculate image size "%s"', $path)
            );
        }
        
        $this->imgSize = array_combine(
            ['width', 'height', 'type', 'attr', 'bits', 'mime'],
            $result     
        );
    }
    
    /**
     * Initialize and validation
     */
    protected function init() 
    {
        $this->validatePath($this->options['path']);
        $this->validateSavePath($this->options['savePath']);
        
        $this->setImgSize($this->options['path']);
        $this->validateType();
        
        $this->setImage($this->options['path']);
    }
    
    /**
     * Validate path
     * 
     * @param string $path
     * @throws InvalidArgumentException
     */
    protected function validatePath($path)
    {
        if(!file_exists($path) || !is_readable($path)) {    
            throw new InvalidArgumentException(
                sprintf('Incorrect path "%s". Image does not exist or not readable.', $path)
            );
        }
    }
    
    /**
     * Validate savePath
     * 
     * @param string $savePath
     * @throws InvalidArgumentException
     */
    protected function validateSavePath($savePath) 
    {
        if (empty($savePath)) {
            return;
        }
        
        $dirPath = dirname($savePath);      
        if(!file_exists($dirPath) && !mkdir($dirPath, 0755, true)) {
            throw new InvalidArgumentException(
                sprintf('Impossible create sub-folders structure for destination "%s"', $savePath)
            );
        }

        if(!is_writable($dirPath)) {
            throw new InvalidArgumentException(
                sprintf('Destination does not have writable permission "%s"', $dirPath)
            );
        }
    }
    
    /**
     * Validate supported types
     * 
     * @throws InvalidArgumentException
     */
    protected function validateType() 
    {
        if (!in_array($this->imgSize['type'], $this->supportedType)) {
            throw new InvalidArgumentException(
                sprintf('Image with type: "%s", mime: "%s" is not supported', $this->imgSize['type'], $this->imgSize['mime'])
            );
        }
    }
}
