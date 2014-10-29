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
     * @retutn string|boolean - full path or false if failed
     */
    protected function getDataPath($path)
    {       
        $fullPath = $this->dataPath . $path;
        $dirPath  = dirname($fullPath);
       
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
            
        return realpath($fullPath);
    }
}