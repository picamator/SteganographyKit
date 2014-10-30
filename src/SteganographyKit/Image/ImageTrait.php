<?php
/**
 * Image Trait
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\Image;

trait ImageTrait
{
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
    public function getImageSize()
    {
        return $this->imgSize;
    }        
    
    /**
     * Gets rgb and alpha of pixel as binary for current pixel coordinat
     * 
     * @param integer $xIndex    x coordinat
     * @param integer $yIndex    y coordinat
     * @return array
     * <code>
            array('red' => ..., 'green' => ..., 'blue' => ..., 'alpha' => ...);
     * </code>
     */
    public function getBinaryColor($xIndex, $yIndex) 
    {        
        $result = $this->getDecimalColor($xIndex, $yIndex);
        foreach($result as &$item) {
            $item = sprintf('%08d', decbin($item));
        }
        unset($item);
        
        return $result;
    } 
    
    /**
     * Gets rgb and alpha of pixel as decimal for current pixel coordinat
     * It works only for truecolor otherwise it should be used imagecolorsforindex
     * 
     * @param integer $xIndex    x coordinat
     * @param integer $yIndex    y coordinat
     * @return array contains
     * <code>
            array('red' => ..., 'green' => ..., 'blue' => ..., 'alpha' => ...);
     * </code>
     */
    public function getDecimalColor($xIndex, $yIndex) 
    {
        $colorIndex = imagecolorat($this->image, $xIndex, $yIndex);
        
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
     * Sets pixel
     * Modify image pixel
     * 
     * @param integer $xIndex
     * @param integer $yIndex
     * @param array $pixel
     * @return self
     * @throws Exception
     */
    public function setPixel($xIndex, $yIndex, array $pixel) 
    {
        $color  = $this->getColorallocate($pixel['red'], $pixel['green'], $pixel['blue']);            

        $result = imagesetpixel($this->image, $xIndex, $yIndex, $color);
        if ($result === false) {
            throw new Exception('Failed to modify pixel [' .$xIndex .', ' . $yIndex . '].' );
        }
        
        return $this;
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
     * @throws Exception
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
            throw new Exception('Can not create image by path ' . $path);
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
     */
    protected function setImgSize($path) 
    {
        $result = getimagesize($path);
        if($result === false) {
            throw new Exception('Imposible to calculate image size: '.$path);
        }
        
        $this->imgSize = array_combine(
            array('width', 'height', 'type', 'attr', 'bits', 'mime'),
            $result     
        );
    }
    
    /**
     * Verify is GB Lib extension was loaded
     * 
     * @throws Exception
     */
    protected function validateGbLib() 
    {
        if(extension_loaded('gd') === false) {
            throw new Exception('GD php extension was not loaded: http://www.php.net/manual/en/book.image.php');
        } 
    }
    
    /**
     * Validate path
     * 
     * @param string $path
     * @throws Exception
     */
    protected function validatePath($path)
    {
        if(!file_exists($path) || !is_readable($path)) {    
            throw new Exception('Incorrect path "' . $path . '". Image does not exsit or not readable.');
        }
    }
    
    /**
     * Validate savePath
     * 
     * @param string $savePath
     * @throws Exception
     */
    protected function validateSavePath($savePath) 
    {
        $dirPath = dirname($savePath);      
        if(!file_exists($dirPath) && !mkdir($dirPath, 0755, true)) {
            throw new Exception('Impossible create subfolders structure for destination "' . $savePath . '"');
        } else if(!is_writable($dirPath)) {
            throw new Exception('Destination does not have writable permission "' . $savePath . '"');
        }
    }
    
    /**
     * Validate supported type
     * 
     * @throws Exception
     */
    protected function validateType() 
    {
        if (!in_array($this->imgSize['type'], $this->supportedType)) {
            throw new Exception('Image with type: "' . $this->imgSize['type'] 
                . '", mime: "' . $this->imgSize['mime'] . '" is not supported.'
            );
        }
    }
}