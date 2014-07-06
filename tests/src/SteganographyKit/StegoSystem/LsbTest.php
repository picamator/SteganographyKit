<?php
/**
 * StegoSystem LSB UnitTest
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoSystem;
use SteganographyKit\BaseTest;
use SteganographyKit\StegoSystem\Lsb;
use SteganographyKit\SecretText\Ascii;
use SteganographyKit\CoverText\PngImg;
use SteganographyKit\StegoText\PngImg as StegoTextPngImg;

class LsbTest extends BaseTest 
{
    /**
     * Count for data provider to Encode-Decode test
     */
    const ENCODE_DECODE_COUNT = 1;
    
    /**
     * @dataProvider providerEncode
     * @param array $optionsCoverText
     * @param array $optionsSecretText
     */
    public function testEncode(array $optionsCoverText, array $optionsSecretText) 
    {                  
        $optionsCoverText['path']       = $this->getDataPath($optionsCoverText['path']);
        $optionsCoverText['savePath']   = dirname($optionsCoverText['path']) . '/'
            . $optionsCoverText['savePath'];
                
        $coverText      = new PngImg($optionsCoverText);  
        $secretText     = new Ascii($optionsSecretText);
        
        $lsb    = new Lsb();        
        $result = $lsb->encode($secretText, $coverText);
        
        $this->assertTrue(file_exists($result));
//        var_dump($result);
    }
    
    /**
     * @dataProvider providerDecode
     * @param array $optionsStegoText
     */
    public function testDecode(array $optionsStegoText, $expected) 
    {      
        $optionsStegoText['path'] = $this->getDataPath($optionsStegoText['path']);

        $stegoText  = new StegoTextPngImg($optionsStegoText);
        $lsb        = new Lsb();    
        $result     = $lsb->decode($stegoText);
        
        $this->assertEquals($expected, $result);
//        var_dump($result);
    }
    
    /**
     * @dataProvider        providerEncodeDecode
     * @param array         $optionsCoverText
     * @param array         $optionsSecretText
     */
    public function testEncodeDecode(array $optionsCoverText, array $optionsSecretText) 
    {           
        // encode
        $optionsCoverText['path']       = $this->getDataPath($optionsCoverText['path']);
        $optionsCoverText['savePath']   = dirname($optionsCoverText['path']) . '/'
            . $optionsCoverText['savePath'];
               
        $coverText      = new PngImg($optionsCoverText);  
        $secretText     = new Ascii($optionsSecretText);
        
        $lsb            = new Lsb();        
        $stegoImgPath   = $lsb->encode($secretText, $coverText);
        
        $this->assertTrue(file_exists($stegoImgPath));
        
        // decode
        $stegoText  = new StegoTextPngImg(array(
            'path' => $stegoImgPath
        ));
        $decodeText = $lsb->decode($stegoText);
        
        $this->assertEquals($optionsSecretText['text'], $decodeText);
    }
    
    public function providerDecode() 
    {
        return array(
            array(
                array(
                    'path' => 'stego/lorem_ipsum_li_200_200.png',
                ),
                'Lorem ipsum Li'
            )
//            array(
//                array(
//                    'path' => 'stego/original_1404658899.1128_secret_length_7329.png',
//                ),
//                ''
//            )
        );
    }
    
    public function providerEncode() 
    {
        return array(
            array(
                array(
                    'path'      => 'original_200_200.png',
                    'savePath'  => 'stego/original_'.date('Y_m_d_H_i_s').'.png'
                ),
                array('text' => 'Lorem ipsum Li'),
            )
        );
    }
    
    /**
     * DataProvider to generate set of encode information
     * to validate how encode-decode is working
     * 
     * @return array
     */
    public function providerEncodeDecode()
    {
        $optionsCoverText = array(
            'path'      => 'original_200_200.png',
            'savePath'  => 'stego/original_%s.png'
        );        

        // 200*200*3/8 = 15000 characters max to cover
        // 19433 secret text length
        $secretText         = file_get_contents($this->getDataPath('secret_text.txt'));
        $secretTextLength   = strlen($secretText);
        
        // generate provider data set
        $providerData = array();
        for($i = 0; $i < self::ENCODE_DECODE_COUNT; $i++) {
            // generate secretText item
//            $textItemStart  = mt_rand(0, 10 - 1);
//            $textItemLength = mt_rand(1, 10); 
            $textItemStart  = mt_rand(0, $secretTextLength - 1);
            $textItemLength = mt_rand(1, $secretTextLength); 
            
            $secretTextItem = substr($secretText,$textItemStart, $textItemLength);
            $secretTextItem = str_shuffle($secretTextItem);
            
            // get coverText options
            $providerData[$i][] = array(
                'path'      => $optionsCoverText['path'],
                'savePath'  => sprintf(
                    $optionsCoverText['savePath'],
                    microtime(true) . '_secret_length_' . $textItemLength    
                )
            );
            
            // set secretText option
            $providerData[$i][] = array(
                'text' => $secretTextItem
            );
        }
           
        
        return $providerData;
    }
}