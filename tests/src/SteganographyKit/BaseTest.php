<?php
/**
 * Base SteganographyKit UnitTest
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit;

class BaseTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * Path to the data folder
     * 
     * @var string 
     */
    protected $dataPath = './data/';
    
    /**
     * Template methods runs once for each test method
     * of the test case class 
     */
    protected function setUp()
    {

    }
    
    /**
     * Gets full path to data
     * 
     * @param string $path
     * @retutn string
     */
    protected function getDataPath($path)
    {        
        return realpath($this->dataPath . $path);
    }
    
        
    /**
     * Gets random channel
     * 
     * @return array
     */
    static protected function getRandomChannel(array $channel, $channelSize = null) 
    {
        $channelSize = $channelSize ? : count($channel);
        
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