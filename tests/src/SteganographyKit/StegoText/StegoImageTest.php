<?php
/**
 * Stego Text PngImg UnitTest
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoText;
use SteganographyKit\BaseTest;

class StegoImageTest extends BaseTest 
{
    /**
     * @dataProvider providerGetBinaryData
     * @param array $options
     */
    public function testGetBinaryData(array $options) 
    {
        $options['path'] = $this->getDataPath($options['path']);
       
        $stegoImg   = new StegoImage($options);
        $imgSize    = $stegoImg->getImageSize();
        
        for ($i = 0; $i < $imgSize['width']; $i++) {
            for ($j = 0; $j < $imgSize['height']; $j++) {
                $result = $stegoImg->getBinaryData($i, $j);
                
                // assert rgb
                $this->assertFalse(empty($result['red']));
                $this->assertFalse(empty($result['green']));
                $this->assertFalse(empty($result['blue']));
            }
        } 
    }
    
    /**
     * @dataProvider providerFailed
     * @expectedException SteganographyKit\InvalidArgumentException
     * @param array $options
     */
    public function testFailed(array $options) 
    {
        new StegoImage($options);
    }
    
    public function providerGetBinaryData() 
    {
        return array(
            array(array('path' => 'original_200_200.png'))
        );
    }
    
    public function providerFailed() 
    {
        return array(
            array(array('path' => 'non_existing_file.png')),
            array(array('path' => 'secret_text.txt'))
        );
    }
}