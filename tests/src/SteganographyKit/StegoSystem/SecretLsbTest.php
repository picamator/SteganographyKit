<?php
/**
 * StegoSystem Secret LSB UnitTest
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoSystem;

use SteganographyKit\StegoKey\PseudoRandomKey;
use SteganographyKit\SecretText\Ascii;
use SteganographyKit\CoverText\PngImg;
use SteganographyKit\StegoText\PngImg as StegoTextPngImg;

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
    protected $secretKey = 12345678;
    
    public function setUp() 
    {
        parent::setUp();
        
        $this->stegoKey   = new PseudoRandomKey($this->secretKey);
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
        // encode
        $optionsCoverText['path']       = $this->getDataPath($optionsCoverText['path']);
        $optionsCoverText['savePath']   = dirname($optionsCoverText['path']) . '/'
            . $optionsCoverText['savePath'];
               
        $coverText      = new PngImg($optionsCoverText);  
        $secretText     = new Ascii($optionsSecretText);
            
        $stegoImgPath   = $this->secretLsb->setUseChannel($useChannel)
            ->encode($secretText, $coverText);
        
        $this->assertTrue(file_exists($stegoImgPath));
        
        // decode
        $stegoText  = new StegoTextPngImg(array(
            'path' => $stegoImgPath
        ));
        
        $this->stegoKey->setSecretKey($this->secretKey);
        $decodeText = $this->secretLsb->decode($stegoText, new Ascii());
        
        $this->assertEquals($optionsSecretText['text'], $decodeText);
    }
    
    /**
     * DataProvider to generate set of encode information
     * to validate how encode-decode is working
     * 
     * @return array
     */
    public function providerEncodeDecode()
    {
       return $this->generateProvider(100, 1000, array('red', 'green', 'blue'));
//       return $this->generateProvider(1, 12000, array('red', 'green', 'blue'));
    }
}

