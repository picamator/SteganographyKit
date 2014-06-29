<?php
/**
 * Stego Text PngImg UnitTest
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoText;
use SteganographyKit\BaseTest;
use SteganographyKit\StegoText\PngImg;

class PngImgTest extends BaseTest 
{
    /**
     * @dataProvider providerGetBinaryData
     * @param array $options
     */
    public function testGetBinaryData(array $options) 
    {
        $options['path'] = $this->getDataPath($options['path']);
        $xStart  = 0;
        $yStart  = 0;
        
        $xEnd    = 1000;
        $yEnd    = 1000;
      
        $pngImg = new PngImg($options);         
        for ($i = $xStart; $i < $xEnd; $i++) {
            for ($j = $yStart; $j < $yEnd; $j++) {
                $result = $pngImg->getBinaryData($i, $j);
            }
        }    
        
        // assert rgb
        $this->assertFalse(empty($result['red']));
        $this->assertFalse(empty($result['green']));
        $this->assertFalse(empty($result['blue']));
    }
    
    public function providerGetBinaryData() 
    {
        return array(
            array(array('path' => 'original.png'))
        );
    }
}