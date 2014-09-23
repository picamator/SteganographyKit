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
    protected $image;
    
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
            $item = str_pad(decbin($item), 8, '0', STR_PAD_LEFT);
        }
        unset($item);
        
        return $result;
    } 
    
    /**
     * Gets rgb and alpha of pixel as decimal for current pixel coordinat
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
        
        return imagecolorsforindex($this->image, $colorIndex);
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
            throw new Exception('Can not create png image by original one');
        }
        
        $this->image = $image;
    }
}