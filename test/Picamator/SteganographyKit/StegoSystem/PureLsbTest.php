<?php
/**
 * StegoSystem Pure LSB UnitTest
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

use Picamator\SteganographyKit\StegoSystem\PureLsb;
use Picamator\SteganographyKit\SecretText\PlainText;
use Picamator\SteganographyKit\Image\Image;

class PureLsbTest extends BaseLsbTest
{   
    /**
     * Pure LSB StegoSystem
     * 
     * @var PureLsb 
     */
    protected $pureLsb;
    
    public function setUp() 
    {
        parent::setUp();
        $this->pureLsb = new PureLsb();  
    }

    /**
     * @dataProvider providerEncode
     * @param array $optionsCoverText
     * @param array $optionsSecretText
     */
    public function testEncode(array $optionsCoverText, array $optionsSecretText) 
    {                  
        $optionsCoverText['path']       = $this->getDataPath($optionsCoverText['path']);
        $optionsCoverText['savePath']   = $this->getDataPath(self::$stegoPath) . '/'
            . $optionsCoverText['savePath'];
                
        $coverText      = new Image($optionsCoverText);  
        $secretText     = new PlainText($optionsSecretText);
        
        $result = $this->pureLsb->encode($secretText, $coverText);
        
        $this->assertTrue($result);
    }
    
    /**
     * @dataProvider providerDecode
     * @param array $optionsStegoText
     */
    public function testDecode(array $optionsStegoText, $expected) 
    {      
        $optionsStegoText['path'] = $this->getDataPath($optionsStegoText['path']);

        $stegoText  = new Image($optionsStegoText); 
        $result     = $this->pureLsb->decode($stegoText, new PlainText());
        
        $this->assertEquals($expected, $result);
//        var_dump($result);
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
        $this->encodeDecode($optionsCoverText, $optionsSecretText, $useChannel, $this->pureLsb);
    }
    
    public function providerDecode() 
    {
        return array(
            array(
                array(
                    'path' => 'lsb/pure/lorem_ipsum_li_200_200.png',
                ),
                'Lorem ipsum Li'
            )
        );
    }
    
    public function providerEncode() 
    {
        return array(
            array(
                array(
                    'path'      => 'original_200_200.png',
                    'savePath'  => 'original_' . date('Y_m_d_H_i_s') . '.png'
                ),
                array('text' => 'Lorem ipsum Li'),
            )
        );
    }
    
    /**
     * DataProvider to generate set of encode information
     * to validate how encode-decode is working
     * 
     * before optimizaton it's run 1.7 Min and used 9 MB (1, 12000, array('red', 'green', 'blue')
     * 
     * @return array
     */
    public function providerEncodeDecode()
    {
       return $this->generateProvider(100, 1000, array('red', 'green', 'blue'));
//       return $this->generateProvider(1, 12000, array('red', 'green', 'blue'));
    }
}