<?php
namespace Picamator\SteganographyKit\Tests\Unit\Iterator;

use Picamator\SteganographyKit\Iterator\ImageIterator;
use Picamator\SteganographyKit\Tests\Integration\BaseTest;

class ImageIteratorTest extends BaseTest
{
    /**
     * @dataProvider providerIterator
     *
     * @param string $path
     * @param array $imgSize
     * @param integer $expectedSize
     */
    public function testIterator($path, array $imgSize, $expectedSize) 
    {
        $path = $this->getDataPath($path);
        
        // image mock
        $imageMock = $this->getMockBuilder('Picamator\SteganographyKit\Image\Image')
            ->setMethods(['getSize', 'getImage'])
            ->setConstructorArgs([['path' => $path]])
            ->getMock();

        $imageMock->expects($this->once())
            ->method('getSize')->will($this->returnValue($imgSize));    
        
        $imageSrc  = imagecreatefrompng($path);
        $imageMock->expects($this->once())
            ->method('getImage')->will($this->returnValue($imageSrc)); 

        // create iterator
        $iterator   = new ImageIterator($imageMock);

        $i = 0;
        foreach($iterator as $item) {
            $this->assertArrayHasKey('x', $item);
            $this->assertArrayHasKey('y', $item);
            $this->assertArrayHasKey('color', $item);

            $i++;
        }
                
       $this->assertEquals($expectedSize, $i);
    }
    
    public function providerIterator()
    {
        return [
            ['original_200_200.png',    ['width' => 1, 'height' =>  3], 3],
            ['original_200_200.png',    ['width' => 3, 'height' =>  1], 3],
            ['original_200_200.png',    ['width' => 1, 'height' =>  1], 1],
            ['original_200_200.png',    ['width' => 1, 'height' =>  2], 2],
            ['original_50_50.png',      ['width' => 50, 'height' =>  50], 2500],
        ];
    }
}
