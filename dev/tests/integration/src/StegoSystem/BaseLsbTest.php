<?php
namespace Picamator\SteganographyKit\Tests\Integration\StegoSystem;

use Picamator\SteganographyKit\SecretText\PlainText;
use Picamator\SteganographyKit\Image\Image;
use Picamator\SteganographyKit\StegoSystem\StegoSystemInterface;
use Picamator\SteganographyKit\Tests\Integration\BaseTest;

abstract class BaseLsbTest extends BaseTest
{    
    /**
     * SecretText file
     * 
     * @var string
     */
    protected $secretFile = 'secret_text.txt';
    
    /**
     * CoverText options
     * 
     * @var array
     */
    static protected $optionsCoverText = array(
        'path'      => 'original_200_200.png',
        'savePath'  => 'original_%s.png'
    ); 
    
    /**
     * Remove files in stegoPath after runs each test
     */
    public function tearDown()
    {
        $this->clearStegoPath();
    }
    
    /**
     * Base Encode Decode
     * 
     * @param array                 $optionsCoverText
     * @param array                 $optionsSecretText
     * @param array                 $channels
     * @param StegoSystemInterface  $stegoSystem
     */
    protected function encodeDecode(array $optionsCoverText, 
        array $optionsSecretText, array $channels, StegoSystemInterface $stegoSystem    
    ) {           
        // encode
        $optionsCoverText['path']       = $this->getDataPath($optionsCoverText['path']);
        $optionsCoverText['savePath']   = dirname($optionsCoverText['path']) . '/'
            . $optionsCoverText['savePath'];
               
        $coverText      = new Image($optionsCoverText);  
        $secretText     = new PlainText($optionsSecretText);
        
        $encode = $stegoSystem->setChannels($channels)->encode($secretText, $coverText);
        
        $this->assertTrue($encode);
        
        // decode
        $stegoText  = new Image(array('path' => $optionsCoverText['savePath']));
        $decodeText = $stegoSystem->decode($stegoText, $secretText);
        
        $this->assertEquals($optionsSecretText['text'], $decodeText);
    }
    
    /**
     * Generate data provider
     * It's use secret_text.txt as a secret text and
     * original_200_200.png as a cover tet
     * 
     * @param integer $resultCount  - number of providers data
     * @param integer $textLength   - text length, if not set random data is used
     * @param array $channel        - channel, if not set random data is used
     *
     * @return array
     */
    protected function generateProvider($resultCount, 
        $textLength = null, array $channel = []
    ) {              
        $supportedChannel       = array('red', 'green', 'blue');
        $supportedChannelSize   = count($supportedChannel);
        
        // 200*200*3/8 = 15000 characters max to cover
        // 19433 secret text length
        $secretText         = $this->getSecretFileData();
        $secretTextLength   = strlen($secretText);
        
        // validate parameters
        self::validatePrameters($textLength, $secretTextLength, $channel, $supportedChannelSize);
        
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
            $providerData[$i][] = self::getProviderDataItem($useChannel, $secretTextItem);
            
            // set secretText option
            $providerData[$i][] = array('text' => $secretTextItem);
            
            // set use channel
            $providerData[$i][] = $useChannel;                 
        }

        return $providerData;
    }
    
    /**
     * Gets secret data from file
     * 
     * @return string|false
     * @throws \PHPUnit_Framework_Exception
     */
    protected function getSecretFileData() 
    {
        $result = file_get_contents($this->getDataPath($this->secretFile));
        if ($result === false) {
            throw new \PHPUnit_Framework_Exception('Can not read secretFile: ' . $this->secretFile);
        }
        
        return $result;
    }
    
    /**
     * Validate provider paramters
     * 
     * @param integer $textLength
     * @param integer $secretTextLength
     * @param array $channel
     * @param integer $supportedChannelSize
     * @throws \PHPUnit_Framework_Exception
     */
    static protected function validatePrameters($textLength, 
        $secretTextLength, array $channel, $supportedChannelSize
    ) {
        if ((!is_null($textLength) &&  $textLength > $secretTextLength) 
            || (!empty($channel) && count($channel) > $supportedChannelSize)) {
            
            throw new \PHPUnit_Framework_Exception('Used parameters are out of data source length: text ['
                . $secretTextLength . '], channels [' . $supportedChannelSize . ']');  
        }
    }
    
    /**
     * Gets provider's data item
     * 
     * @param array $useChannel
     * @param string $secretTextItem
     * @return array - array('path' => ..., 'savePath' => ...);
     */
    static protected function getProviderDataItem(array $useChannel, $secretTextItem) 
    {
        $args = microtime(true) . '_secret_length_' . strlen($secretTextItem)
            . '_' . implode('_', $useChannel);
        
        $result =  array(
            'path'      => self::$optionsCoverText['path'],
            'savePath'  => self::$stegoPath . '/' . sprintf(self::$optionsCoverText['savePath'], $args)
        );
        
        return $result;
    }
    
    /**
     * Gets random channel
     * 
     * @return array
     */
    static protected function getRandomChannel(array $channel, $channelSize = null) 
    {
        $channelSize    = $channelSize ? : count($channel);  
        $useChannelSize = mt_rand(1, $channelSize);
        $useChannelKey  = (array)array_rand($channel, $useChannelSize);

        $result = [];
        foreach($useChannelKey as $value) {
            $result[] = $channel[$value];
        }
        
        return $result;
    }
    
    static protected function getRandomText($text, $textLength = null) 
    {
        $textLength = $textLength ? : strlen($text);      
        $itemStart  = mt_rand(0, $textLength - 1);
        $itemLength = mt_rand(1, $textLength); 

        $result = substr($text, $itemStart, $itemLength);
        
        return str_shuffle($result);
    }
}