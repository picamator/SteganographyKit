<?php
/**
 * Base SteganographyKit UnitTest
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

use Picamator\SteganographyKit\SecretText\PlainText;

class PlainTextTest extends BaseTest 
{    
    /**
     * @dataProvider providerGetBinaryData
     * @param string $text
     * @param string $expected
     */
    public function testGetBinaryData($text, $expected) 
    {  
        $plainText  = new PlainText(array('text' => $text));
        $actual     = $plainText->getBinaryData();
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @dataProvider providerGetFromBinaryData
     * @param string $binaryData
     * @param string $expected
     */
    public function testGetFromBinaryData($binaryData, $expected) 
    {
        $plainText  = new PlainText();
        $actual     = $plainText->getFromBinaryData($binaryData);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function providerGetBinaryData() 
    {
        return array(
            array(
                'Lorem ipsum',           
                '01100101010010100111101001111010011110010101001100111001010010110111101001010110010110000100100101001100010000110110011101110101011110100101000101010101010000010100011101010001011000110100010101010100011001110011110100111101'
                . PlainText::END_TEXT_MARK
            )
        );
    }
    
    public function providerGetFromBinaryData() 
    {
        return array(
            array(
                '01100101010010100111101001111010011110010101001100111001010010110111101001010110010110000100100101001100010000110110011101110101011110100101000101010101010000010100011101010001011000110100010101010100011001110011110100111101'
                . PlainText::END_TEXT_MARK,
                'Lorem ipsum'
            )
        );
    }
}