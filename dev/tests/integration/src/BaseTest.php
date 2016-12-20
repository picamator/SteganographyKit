<?php
namespace Picamator\SteganographyKit\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Picamator\SteganographyKit\ObjectManager\ObjectManager;

abstract class BaseTest extends TestCase
{
    /**
     * Path to steganography folder
     * 
     * @var string 
     */
    static protected $stegoPath = 'stego';

    /**
     * Path to the data folder
     *
     * @var string
     */
    private $dataPath = '/data/';

    protected function setUp()
    {
        ObjectManager::cleanInstance();
    }

    /**
     * Gets full path to data
     * 
     * @param string $path
     *
     * @return string|boolean - full path or false if failed
     */
    protected function getDataPath($path)
    {       
        $fullPath = __DIR__ . $this->dataPath . $path;
        $dirPath  = (is_file($fullPath)) ? dirname($fullPath) : $fullPath;
       
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        return $fullPath;
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

        rmdir($path);
    }
}
