<?php
/**
 * Option Trait
 * Handle setting and validate required options
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace Picamator\SteganographyKit\Options;

trait OptionsTrait
{
    /**
     * Options
     * 
     * @var array
     */
    protected $options;
    
    /**
     * Sets options
     * 
     * @param array $options
     */
    protected function setOptions(array $options) 
    {        
        $this->options = array_merge($this->options, $options);
    }
}