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
     * @throws Exception
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
}