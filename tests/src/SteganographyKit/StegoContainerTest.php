<?php
/**
 * StegoContainer SteganographyKit UnitTest
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit;
use SteganographyKit\BaseTest;
use SteganographyKit\StegoContainer;

class StegoContainerTest extends BaseTest 
{
    /**
     * Stego container
     * 
     * @var StegoContainer 
     */
    protected $stegoContainer;
    
    public function setUp() 
    {
        parent::setUp();
        $this->stegoContainer = new StegoContainer();
    }
    
    /**
     * Remove files in stegoPath after runs each test
     */
    public function tearDown()
    {
        $this->clearStegoPath();
    }
    
    /**
     * @dataProvider providerEncode
     * @param string $coverPath
     * @param string $stegoPath
     * @param string $text
     */
    public function testEncode($coverPath, $stegoPath, $text) 
    {       
        $coverPath = $this->getDataPath($coverPath);
        $stegoPath = $this->getDataPath(self::$stegoPath) . '/' . $stegoPath;
        
        $result = $this->stegoContainer->encode($coverPath, $stegoPath, $text);
        
        $this->assertTrue($result);
    }
    
    /**
     * @dataProvider providerDecode
     * @param string $stegoPath
     * @param string $expected
     */
    public function testDecode($stegoPath, $expected) 
    {
        $stegoPath = $this->getDataPath($stegoPath);
        
        $actual = $this->stegoContainer->decode($stegoPath);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function providerEncode()
    {
        return array(
            array('original_200_200.png', 'stego_origin_200_200.png', 'Лорем іпсум, Lorem ipsum, Łorem ipsóm')
        );
    }
    
    public function providerDecode() 
    {
        return array(
            array('lsb/pure/stego_origin_200_200.png', 'Лорем іпсум, Lorem ipsum, Łorem ipsóm')
        );
    }
}
