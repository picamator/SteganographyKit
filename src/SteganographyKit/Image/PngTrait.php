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
     * Gets rgb binary by pixel coordinats
     * 
     * @param integer $xIndex    x coordinat
     * @param integer $yIndex    y coordinat
     * @return array contains binary rgb representation of setting dot
     * <code>
            array('red' => ..., 'green' => ..., 'blue' => ...);
     * </code>
     * @throws Exception
     */
    public function getBinaryColor($xIndex, $yIndex) 
    {        
        $colorIndex = imagecolorat($this->image, $xIndex, $yIndex);
        $colorTran  = imagecolorsforindex($this->image, $colorIndex);
        
        $result = array(
            'red'   => $colorTran['red'],
            'green' => $colorTran['green'],
            'blue'  => $colorTran['blue']
        );       
        foreach($result as &$item) {
            $item = str_pad(decbin($item), 7, '0', STR_PAD_LEFT);
        }
        unset($item);
        
        return $result;
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