<?php
/**
 * StegoSystem Pure LSB UnitTest
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoSystem;

use SteganographyKit\BaseTest;
use SteganographyKit\SecretText\Ascii;
use SteganographyKit\CoverText\PngImg;
use SteganographyKit\StegoText\PngImg as StegoTextPngImg;

class PureLsbTest extends BaseTest 
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
        $optionsCoverText['savePath']   = dirname($optionsCoverText['path']) . '/'
            . $optionsCoverText['savePath'];
                
        $coverText      = new PngImg($optionsCoverText);  
        $secretText     = new Ascii($optionsSecretText);
           
        $result = $this->pureLsb->encode($secretText, $coverText);
        
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
        $result     = $this->pureLsb->decode($stegoText, new Ascii());
        
        $this->assertEquals($expected, $result);
//        var_dump($result);
    }
    
    /**
     * @dataProvider        providerEncodeDecode
     * @param array         $optionsCoverText
     * @param array         $optionsSecretText
     * @param array         $useChannel
     */
    public function testEncodeDecode(
        array $optionsCoverText, 
        array $optionsSecretText,
        array $useChannel    
    ) {           
        // encode
        $optionsCoverText['path']       = $this->getDataPath($optionsCoverText['path']);
        $optionsCoverText['savePath']   = dirname($optionsCoverText['path']) . '/'
            . $optionsCoverText['savePath'];
               
        $coverText      = new PngImg($optionsCoverText);  
        $secretText     = new Ascii($optionsSecretText);
            
        $stegoImgPath   = $this->pureLsb->setUseChannel($useChannel)
            ->encode($secretText, $coverText);
        
        $this->assertTrue(file_exists($stegoImgPath));
        
        // decode
        $stegoText  = new StegoTextPngImg(array(
            'path' => $stegoImgPath
        ));
        $decodeText = $this->pureLsb->decode($stegoText, new Ascii());
        
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
        );
    }
    
    public function providerEncode() 
    {
        return array(
            array(
                array(
                    'path'      => 'original_200_200.png',
                    'savePath'  => 'stego/original_' . date('Y_m_d_H_i_s') . '.png'
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
    
    /**
     * Generate data provider
     * It's use secret_text.txt as a secret text and
     * original_200_200.png as a cover tet
     * 
     * @param integer $resultCount  - number of providers data
     * @param integer $textLength   - text length, if not set random data is used
     * @param integer $channel      - channel, if not set random data is used
     */
    protected function generateProvider($resultCount, 
        $textLength = null, array $channel = []
    ) {
        $optionsCoverText = array(
            'path'      => 'original_200_200.png',
            'savePath'  => 'stego/original_%s.png'
        );        
              
        $supportedChannel       = array('red', 'green', 'blue');
        $supportedChannelSize   = count($supportedChannel);
        
        // 200*200*3/8 = 15000 characters max to cover
        // 19433 secret text length
        $secretText         = file_get_contents($this->getDataPath('secret_text.txt'));
        $secretTextLength   = strlen($secretText);
        
        // validate parameters
        if ((!is_null($textLength) &&  $textLength > $secretTextLength) 
            || (!empty($channel) && count($channel) > $supportedChannelSize)) {
            
            throw new PHPUnit_Framework_Exception('Used parameters are out of data source length: text ['
                . $secretTextLength . '], channels [' . $supportedChannelSize . ']');  
        }
        
        // generate provider data set
        $providerData = [];
        for($i = 0; $i < $resultCount; $i++) {
            // generate secretText item 
            $secretTextItem = (is_null($textLength))?
                self::getRandomText($secretText, $secretTextLength): 
                substr($secretText, 0, $textLength);
            
            // generate use channel      
            $useChannel = (empty($channel))? 
                self::getRandomChannel($supportedChannel, $supportedChannelSize): 
                $channel;
            
            // set coverText options
            $providerData[$i][] = self::getProviderDataItem($optionsCoverText, $useChannel, $secretTextItem);
            
            // set secretText option
            $providerData[$i][] = array('text' => $secretTextItem);
            
            // set use channel
            $providerData[$i][] = $useChannel;                 
        }

        return $providerData;
    }
    
    /**
     * Gets provider's data item
     * 
     * @param array $options
     * @param array $useChannel
     * @param string $secretTextItem
     * @return array - array('path' => ..., 'savePath' => ...);
     */
    static protected function getProviderDataItem(array $options,
        array $useChannel, $secretTextItem) 
    {
        $args = microtime(true) . '_secret_length_' . strlen($secretTextItem)
            . '_' . implode('_', $useChannel);
        
        $result =  array(
            'path'      => $options['path'],
            'savePath'  => sprintf($options['savePath'], $args)
        );
        
        return $result;
    }
}