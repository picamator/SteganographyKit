<?php
/**
 * ImageIterator SteganographyKit UnitTest
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace Picamator\SteganographyKit\Tests\Integration\Iterator;

use Picamator\SteganographyKit\Iterator\ImageIterator;
use Picamator\SteganographyKit\Tests\Integration\BaseTest;

class ImageIteratorTest extends BaseTest
{
    /**
     * @dataProvider providerIterator
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
        $iterator   = new ImageIterator($image);
        $actual     = iterator_to_array($iterator);
                
        $this->assertEquals($expectedSize, count($actual)); 
    }
    
    public function providerIterator()
    {
        return array(
            array('original_200_200.png', array('width' => 1, 'height' =>  3), 3),
            array('original_200_200.png', array('width' => 3, 'height' =>  1), 3),
            array('original_200_200.png', array('width' => 1, 'height' =>  1), 1),
            array('original_200_200.png', array('width' => 1, 'height' =>  2), 2),
            array('original_50_50.png', array('width' => 50, 'height' =>  50), 2500),
        );
    }
}
