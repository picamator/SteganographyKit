<?php
/**
 * Option Trait
 * Handle setting and validate required options
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\Options;

trait OptionsTrait
{
    /**
     * Options
     * 
     * @var array
     */
    protected $options = array();
    
    /**
     * Sets options
     * 
     * @param array $options
     */
    protected function setOptions(array $options) 
    {        
        $this->options = array_merge($this->options, $options);
    }
    
    /**
     * Validate path option
     * 
     * @param array $options
     * @throws Exception
     */
    static protected function validatePath(array $options)
    {
        if(!isset($options['path'])
            || !file_exists($options['path']) 
            || !is_readable($options['path'])
        ) {    
            throw new Exception('Incorrect path. Image does not exsit or not readable.');
        }
    }
    
    /**
     * Validate savePath option
     * 
     * @param array $options
     * @throws Exception
     */
    static protected function validateSavePath(array $options) 
    {
        if(!isset($options['savePath'])) {    
            throw new Exception('Incorrect savePath. Option is not set.');
        } 

        $dirPath = dirname($options['savePath']);      
        if(!file_exists($dirPath) && !mkdir($dirPath, 0755, true)) {
            throw new Exception('Incorect savePath. Impossible create subfolders structure for destination'. $options['savePath']);
        } else if(!is_writable($dirPath)) {
            throw new Exception('Incorect savePath. Destination does not have writable permission '. $options['savePath']);
        }
    }
}