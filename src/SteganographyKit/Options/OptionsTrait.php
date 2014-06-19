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
        foreach($this->options as $key => &$value) {
            if(isset($options[$key])) {
                $value = $options[$key];
            }
        }
        unset($value);
    }
    
    /**
     * Validate path option
     * 
     * @param array $options
     * @throws Exception
     */
    protected function validatePath(array $options)
    {
        if(!isset($options['path'])
            || !file_exists($options['path']) 
            || !is_readable($options['path'])
        ) {    
            throw new Exception('Incorrect path. Image does not exsit or not readable.');
        }
    }
}