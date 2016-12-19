<?php
namespace Picamator\SteganographyKit\Tests\Integration\StegoKey;

use Picamator\SteganographyKit\StegoKey\RandomKey;
use Picamator\SteganographyKit\Tests\Integration\BaseTest;

class RandomKeyTest extends BaseTest
{
    /**
     * Stegonagraphy key
     * 
     * @var StegoKey 
     */
    protected $stegoKey;
    
    public function setUp()
    {
        parent::setUp();
        $this->stegoKey = new RandomKey();
    }
    
    /**
     * @dataProvider providerFailedSecretKey
     * @expectedException \Picamator\SteganographyKit\InvalidArgumentException
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
        
        $this->assertGreaterThanOrEqual(RandomKey::MIN_SECRET_KEY_LENGTH, strlen($secretKey));
        $this->assertLessThanOrEqual(RandomKey::MAX_SECRET_KEY_LENGTH, strlen($secretKey));
    }
    
    public function testGenerateSecretKeyAutoSet() 
    {
        $expected   = $this->stegoKey->generateSecretKey(true);
        $actual     = $this->stegoKey->getSecretKey();
        
        $this->assertEquals($expected, $actual);
    }
        
    public function providerFailedSecretKey() 
    {
        return array(
            array(str_repeat('t', RandomKey::MIN_SECRET_KEY_LENGTH - 1)),
            array((int)str_repeat('9', RandomKey::MAX_SECRET_KEY_LENGTH + 1)),
            array('test')
        );
    }
    
    public function providerSuccessSecretKey() 
    {
        return array(
            array((int)str_repeat('1', RandomKey::MIN_SECRET_KEY_LENGTH)),
            array((int)str_repeat('1', RandomKey::MAX_SECRET_KEY_LENGTH)),
        );
    }
}
