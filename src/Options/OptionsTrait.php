<?php
namespace Picamator\SteganographyKit\Options;

/**
 * Option Trait
 * Handle setting and validate required options
 */
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
     * @param array $optionsDefault
     */
    protected function setOptions(array $options, array $optionsDefault = array()) 
    {        
        $this->options = array_merge($optionsDefault, $options);
    }
}
