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
        
        $pngImg     = new PngImg($options); 
        $result     = $pngImg->getBinaryData(10, 10);
        var_dump($result);
    }
    
    public function providerGetBinaryData() 
    {
        return array(
            array(array('path' => 'original.png'))
        );
    }
}