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
        $result = substr($binaryData, 0, $endMarkPos);
        $result = str_split($result, self::TEXT_ITEM_LENGTH);  
        
        // last item would not have enouph data
        $lastIndex = count($result) - 1;
        $result[$lastIndex] = str_pad(
            $result[$lastIndex], 
            self::TEXT_ITEM_LENGTH, 
            '0', 
            STR_PAD_RIGHT
        );
        
        $result = array_map(
            function($value) {
                return chr(bindec($value));
            }, 
            $result
        );
                
        return implode('', $result);
    }
}