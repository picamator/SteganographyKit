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
     * Gets decretText from binary data
     * 
     * @param string    $binaryData - raw secretText with endMark
     * @param integer   $endMarkPos - position of endMark
     * @return string
     */
    static public function getFromBinaryData($binaryData, $endMarkPos) 
    {
        // remove endText mark
        $dataFiltered = self::removeEndMark($binaryData, $endMarkPos);
        
        // convert ascii binary code to char
        return self::convertBinaryToChar($dataFiltered);
    }
    
    /**
     * Convert binaru data to char
     * 
     * @param string $binaryData
     * @return string
     */
    static protected function convertBinaryToChar($binaryData) 
    {
        $pattern    = '/[01]{' . self::TEXT_ITEM_LENGTH . '}/';
        $result     = preg_replace_callback(
            $pattern, 
            function($match) { 
                return chr(bindec($match[0])); 
            }, 
            $binaryData
        );

        return $result;
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
}