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
     * Length of secretText item
     */
    const TEXT_ITEM_LENGTH = 8;
    
    /**
     * Encoded text to ASCII
     *
     * @var string 
     */
    protected $encodedText;
    
    /**
     * Cache container
     * 
     * @var array 
     */
    static protected $cache = array();
    
    /**
     * Gets size data in bit
     * 
     * @return integer
     */
    public function getSize() 
    {
        return strlen($this->encodedText) * self::TEXT_ITEM_LENGTH 
            + strlen(self::END_TEXT_MARK);
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
     * Remove text endMark
     * 
     * @param string    $binaryData - raw secretText with endMark
     * @param integer   $endMarkPos - position of endMark
     * @return string
     */
    static protected function removeEndMark($binaryData, $endMarkPos) {
        // remove endText mark
        $result = substr($binaryData, 0, $endMarkPos);
        
        // it's possible remove some seros from last character
        $missingZero = strlen($result) % self::TEXT_ITEM_LENGTH;
        if ($missingZero !== 0) {
            $result .= str_repeat('0', $missingZero);
        }
        
        return $result;
    }
    
    /**
     * Set to cache
     * 
     * @param string $key
     * @param string $value
     */
    static protected function setToCache($key, $value) 
    {
        self::$cache[$key] = $value;
    }
    
    /**
     * Gets from cache
     * 
     * @return string|false
     */
    static protected function getFromCache($key) 
    {
        return (key_exists($key, self::$cache)) ? self::$cache[$key] : false;
    }
}