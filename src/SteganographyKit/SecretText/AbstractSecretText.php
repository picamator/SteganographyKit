<?php
/**
 * Abstract for Secret Text
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\SecretText;
use SteganographyKit\Options\OptionsTrait;

abstract class AbstractSecretText implements SecretTextInterface 
{    
    use OptionsTrait;
    
    /**
     * Mark that is added to end of the text
     * it helps to identify where secret text end
     */
    const END_TEXT_MARK = '0000000000000000';
        
    /**
     * Data Options
     * 
     * @var array 
     */
    protected $dataOptions = array();
    
    /**
     * @param array $options
     */
    public function __construct(array $options = array()) 
    {
        $this->setOptions($options);
    }
        
    /**
     * Gets position of end mark
     * 
     * @param string $secretText
     * @return integer|false
     */
    public function getEndMarkPos($secretText) 
    {
        return strpos($secretText, self::END_TEXT_MARK);
    }
     
    /**
     * Sets Data Options
     * 
     * @param array $dataOptions
     * @return self
     * @throws SteganographyKit\InvalidArgumentException
     */
    public function setDataOptions(array $dataOptions) 
    {
        $this->dataOptions = array_merge($this->dataOptions, $dataOptions);
        
        return $this;
    }
}