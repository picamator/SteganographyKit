<?php
namespace Picamator\SteganographyKit\Tests\Integration\StegoSystem;

use Picamator\SteganographyKit\StegoSystem\SecretLsb;
use Picamator\SteganographyKit\StegoKey\RandomKey;

class SecretLsbTest extends BaseLsbTest 
{   
    /**
     * Pure LSB StegoSystem
     * 
     * @var StegoSystemInterface 
     */
    protected $secretLsb;
    
    /**
     * StegoKey
     *
     * @var StegoKeyInterface 
     */
    protected $stegoKey;
    
    /**
     * SecretKey
     * 
     * @var integer 
     */
    protected $secretKey = 1234;
    
    public function setUp() 
    {
        parent::setUp();
        
        $this->stegoKey   = new RandomKey($this->secretKey);
        $this->secretLsb  = new SecretLsb();
        $this->secretLsb->setStegoKey($this->stegoKey);
    }
    
    /**
     * @dataProvider        providerEncodeDecode
     * @param array         $optionsCoverText
     * @param array         $optionsSecretText
     * @param array         $useChannel
     */
    public function testEncodeDecode(array $optionsCoverText, 
        array $optionsSecretText, array $useChannel    
    ) {           
        $this->encodeDecode($optionsCoverText, $optionsSecretText, $useChannel, $this->secretLsb);
    }
    
    /**
     * DataProvider to generate set of encode information
     * to validate how encode-decode is working
     * 
     * @return array
     */
    public function providerEncodeDecode()
    {
       return $this->generateProvider(10, 1000);
//       return $this->generateProvider(1, 3500, array('red', 'green', 'blue'));
    }
}

