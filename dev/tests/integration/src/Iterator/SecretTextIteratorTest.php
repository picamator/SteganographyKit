<?php
namespace Picamator\SteganographyKit\Tests\Integration\Iterator;

use Picamator\SteganographyKit\Iterator\SecretTextIterator;
use Picamator\SteganographyKit\Tests\Integration\BaseTest;

class SecretTextIteratorTest extends BaseTest
{
    /**
     * @dataProvider providerIterator
     * @param string $binaryData
     * @param integer $itemSize
     * @param array $expected
     */
    public function testIterator($binaryData, $itemSize, array $expected) 
    {        
        // mock image
        $secretText = $this->getMockBuilder('Picamator\SteganographyKit\SecretText\PlainText')
            ->setMethods(['getBinaryData', 'getBinaryItemSize'])
            ->getMock();
        $secretText->expects($this->once())
            ->method('getBinaryData')->will($this->returnValue($binaryData));    
        
        $secretText->expects($this->once())
            ->method('getBinaryItemSize')->will($this->returnValue($itemSize)); 

        // cretate iterator
        $iterator   = new SecretTextIterator($secretText);
        $actual     = iterator_to_array($iterator);
        
        $this->assertEquals($expected, array_values($actual)); 
    }
    
    public function providerIterator()
    {
        return array(
            array(
                '10011101101010111111111000000000', 
                 3, 
                array(
                    '100',
                    '111',
                    '011',
                    '010',
                    '101',
                    '111',
                    '111',
                    '110',
                    '000',
                    '000',
                    '00',
                )
            ),
            array(
                '10011', 
                 1, 
                array(
                    '1',
                    '0',
                    '0',
                    '1',
                    '1'
                )
            ),
        );
    }
}
