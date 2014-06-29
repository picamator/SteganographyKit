<?php
/**
 * StegoSystem LSB UnitTest
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoSystem;
use SteganographyKit\BaseTest;
use SteganographyKit\StegoSystem\Lsb;
use SteganographyKit\SecretText\Ascii;
use SteganographyKit\CoverText\PngImg;

class LsbTest extends BaseTest 
{
    /**
     * @dataProvider providerEncode
     * @param array $optionsCoverText
     * @param array $optionsSecretText
     */
    public function testEncode(array $optionsCoverText, array $optionsSecretText) 
    {        
        $optionsCoverText['path']       = $this->getDataPath($optionsCoverText['path']);
        $optionsCoverText['savePath']   = dirname($optionsCoverText['path']) . '/'
            . $optionsCoverText['savePath'];
                
        $coverText      = new PngImg($optionsCoverText);  
        $secretText     = new Ascii($optionsSecretText);
        
        $lsb    = new Lsb();        
        $result = $lsb->encode($secretText, $coverText);
        
        $this->assertTrue(file_exists($result));
//        var_dump($result);
    }
    
    public function providerEncode() 
    {
        return array(
            array(
                array(
                    'path'      => 'original.png',
                    'savePath'  => 'stego/original_'.date('Y_m_d_H_i_s').'.png'
                ),
                array('text' => 'Lorem ipsum'),
            )
        );
    }
}