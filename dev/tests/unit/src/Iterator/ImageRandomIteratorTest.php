<?php
namespace Picamator\SteganographyKit\Tests\Unit\Iterator;

use Picamator\SteganographyKit\Iterator\ImageRandomIterator;
use Picamator\SteganographyKit\Tests\Integration\BaseTest;

class ImageRandomIteratorTest extends BaseTest
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
        
        // mock image
        $image = $this->getMockBuilder('Picamator\SteganographyKit\Image\Image')
            ->setMethods(['getSize', 'getImage'])
            ->setConstructorArgs([['path' => $path]])
            ->getMock();
        $image->expects($this->once())
            ->method('getSize')->will($this->returnValue($imgSize));    
        
        $imageSrc   = imagecreatefrompng($path);
        $image->expects($this->once())
            ->method('getImage')->will($this->returnValue($imageSrc)); 

        // create iterator
        $iterator   = new ImageRandomIterator($image, 123456);

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
            ['original_50_50.png', ['width' => 1, 'height' =>  3], 3],
            ['original_50_50.png', ['width' => 3, 'height' =>  1], 3],
            ['original_50_50.png', ['width' => 1, 'height' =>  1], 1],
            ['original_50_50.png', ['width' => 1, 'height' =>  2], 2],
            ['original_50_50.png', ['width' => 50, 'height' =>  50], 2500]
        ];
    }
}
