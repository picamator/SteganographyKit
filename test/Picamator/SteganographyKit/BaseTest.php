<?php
/**
 * Base SteganographyKit UnitTest
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

abstract class BaseTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Path to the data folder
     * 
     * @var string 
     */
    protected $dataPath = './test/Picamator/SteganographyKit/data/';
       
    /**
     * Path to steganography folder
     * 
     * @var string 
     */
    static protected $stegoPath = 'stego';
    
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
    
    /**
     * Clear stego path
     */
    protected function clearStegoPath()
    {
        $path = $this->getDataPath(self::$stegoPath); 
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if($fileInfo->isDot()) {
                continue;
            }
            
            unlink($fileInfo->getPathname());
        }
    }
}