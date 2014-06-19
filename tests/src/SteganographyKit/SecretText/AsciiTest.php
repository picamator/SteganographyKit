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
     */
    public function testGetBinaryData(array $options, array $expected) 
    {
        $asciiText  = new Ascii($options); 
        $result     = $asciiText->getBinaryData();
        $compare    = \array_diff($result, $expected);
        
        $this->assertTrue(empty($compare));
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
            array(
               '1001100',
               '1101111',
               '1110010',
               '1100101',
               '1101101',
               '0100000',
               '1101001',
               '1110000',
               '1110011',
               '1110101',
               '1101101',
                Ascii::END_TEXT_MARK
            ))
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
