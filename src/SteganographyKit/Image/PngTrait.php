<?php
/**
 * Png image Trait
 * Handle to get binary dat of pixel from png image
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\Image;

trait PngTrait
{     
    /**
     * Link to image resource
     * 
     * @var resource 
     */
    protected $image = null;
    
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
     * Sets Image
     * 
     * @throws Exception
     */
    protected function setImage($path) 
    {
        $image  = imagecreatefrompng($path);         
        if($image === false) {
            throw new Exception('Can not create png image by original one.');
        }
        
        if (!is_null($this->image)) {
            // free memory
            imagedestroy($this->image);
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
}