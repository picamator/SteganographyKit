<?php
/**
 * Base SteganographyKit UnitTest
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
        $options['path'] = $this->dataPath . '/' . $options['path'];
        $xIndex = 10;
        $yIndex = 10;
        
        $pngImg = new PngImg($options); 
        $result = $pngImg->getBinaryData($xIndex, $yIndex);
        foreach($result[0] as $item) {
            $this->assertFalse(empty($item['red']));
            $this->assertFalse(empty($item['green']));
            $this->assertFalse(empty($item['blue']));
        }      
        $this->assertEquals($xIndex, count($result[0]));
    }
    
    public function providerGetBinaryData() 
    {
        return array(
            array(array('path' => 'original.png'))
        );
    }
}