<?php
/**
 * StegoSystem LSB UnitTest
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoKey;
use SteganographyKit\BaseTest;

class PseudoRandomKeyTest extends BaseTest 
{
    /**
     * Stegonagraphy key
     * 
     * @var StegoKey 
     */
    protected $stegoKey;
    
    public function setUp()
    {
        $this->stegoKey = new PseudoRandomKey();
    }
    
    /**
     * @dataProvider providerFailedSecretKey
     * @expectedException Exception
     * @param string $secretKey
     */
    public function testFailedSecretKey($secretKey)  
    {
        $this->stegoKey->setSecretKey($secretKey);
    }
    
    /**
     * @dataProvider providerSuccessSecretKey
     * @param integer $secretKey
     */
    public function testSuccessSecretKey($secretKey) 
    {
        $result = $this->stegoKey->setSecretKey($secretKey);
        
        $this->assertTrue(is_object($result));
    }
    
    public function testGenerateSecretKey() 
    {
        $secretKey = $this->stegoKey->generateSecretKey();
        
        $this->assertGreaterThanOrEqual(PseudoRandomKey::MIN_SECRET_KEY_LENGTH, strlen($secretKey));
        $this->assertLessThanOrEqual(PseudoRandomKey::MAX_SECRET_KEY_LENGTH, strlen($secretKey));
    }
    
    public function testGenerateSecretKeyAutoSet() 
    {
        $expected   = $this->stegoKey->generateSecretKey(true);
        $actual     = $this->stegoKey->getSecretKey();
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @dataProvider providerGetCoordinate
     * @param integer $xMax
     * @param integer $yMax
     * @param integer $count
     */
    public function testGetCoordinate($xMax, $yMax, $count) 
    {
        $this->stegoKey->generateSecretKey(true);
        for ($i = 0; $i < $count; $i ++) {
            $coordinats = $this->stegoKey->getCoordinate($xMax, $yMax);
                        
            $this->assertTrue(is_array($coordinats));
            $this->assertCount(2, $coordinats);
            
            $this->assertFalse(empty($coordinats['x']));
            $this->assertFalse(empty($coordinats['y']));
            
            $this->assertLessThanOrEqual($xMax, $coordinats['x']);
            $this->assertLessThanOrEqual($yMax, $coordinats['y']);
        }
    }
    
    public function providerFailedSecretKey() 
    {
        return array(
            array(str_repeat('t', PseudoRandomKey::MIN_SECRET_KEY_LENGTH - 1)),
            array((int)str_repeat('9', PseudoRandomKey::MAX_SECRET_KEY_LENGTH + 1)),
            array('test')
        );
    }
    
    public function providerSuccessSecretKey() 
    {
        return array(
            array((int)str_repeat('1', PseudoRandomKey::MIN_SECRET_KEY_LENGTH)),
            array((int)str_repeat('1', PseudoRandomKey::MAX_SECRET_KEY_LENGTH)),
        );
    }
    
    public function providerGetCoordinate() 
    {
        return array(
            array(200, 200, 10)
        );
    }
}

