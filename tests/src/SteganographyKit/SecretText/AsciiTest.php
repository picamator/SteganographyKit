<?php
/**
 * Base SteganographyKit UnitTest
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\SecretText;
use SteganographyKit\BaseTest;
use SteganographyKit\SecretText\Ascii;

class AsciiTest extends BaseTest 
{
    /**
     * @dataProvider providerGetBinaryData
     * @param array $options
     * @param string $expected
     */
    public function testGetBinaryData(array $options, $expected) 
    {
        $asciiText  = new Ascii($options); 
        $result     = $asciiText->getBinaryData();
        
        $this->assertEquals($expected, $result);
    }
    
    /**
     * @dataProvider providerGetSize
     * @param array $options
     * @param integer $expected
     */
    public function testGetSize(array $options, $expected)
    {
        $asciiText  = new Ascii($options); 
        $result     = $asciiText->getSize();
                
        $this->assertEquals($expected, $result);
    }         
    
    public function providerGetBinaryData() 
    {
        return array(array(
            array('text' => 'Lorem ipsum'),           
                '0100110001101111011100100110010101101101001000000110100101110000011100110111010101101101'.Ascii::END_TEXT_MARK
            )
        );
    }
    
    public function providerGetSize() 
    {
        return array(array(
            array('text' => 'Lorem ipsum'),
            77
        ));
    }
}
