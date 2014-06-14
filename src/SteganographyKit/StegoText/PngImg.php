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

class PngImg extends AbstractStegoText 
{   
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
     * Gets converted data to binary format
     * 
     * @param integer $xIndex max x index
     * @param integer $yIndex max y index
     * @return array contains binary rgb representation of image
     * <code>
     *  array(
     *      0 => array(0 => array('red' => ..., 'green' => ..., 'blue' => ...)),
     *      ...
     *  );
     * </code>
     */
    public function getBinaryData($xIndex = null, $yIndex = null) 
    {
        // identify max (x, y)
        $getIndex = function($index, $default) {
            return (is_null($index) || $index > $default)? $default: intval($index);
        };
        
        $xIndex = $getIndex($xIndex, $this->imgSize['width']);
        $yIndex = $getIndex($yIndex, $this->imgSize['height']);
        
        $result = array();        
        $image  = imagecreatefrompng($this->options['path']);    
        for ($i = 0; $i < $xIndex; $i++) {
            for ($j = 0; $j < $yIndex; $j++) {  
                $colorIndex = imagecolorat($image, $i, $j);
                $colorTran  = imagecolorsforindex($image, $colorIndex);
                
                $result[$i][$j] = $this->getBinaryColor($colorTran);
            }
        }
        
        return $result;
    }
    
    /**
     * Gets binary color
     * 
     * @param array $colorTran
     * @return array
     */
    protected function getBinaryColor(array $colorTran) 
    {
        $result = array(
            'red'   => $colorTran['red'],
            'green' => $colorTran['green'],
            'blue'  => $colorTran['blue']
        );       
        foreach($result as &$item) {
            $item = decbin($item);
            $item = str_pad($item, 7, '0', STR_PAD_LEFT);
        }
        unset($item);
        
        return $result;
    }
}