<?php
/**
 * Secret Text are going to converted binary reprezentation of ASCII code
 * 
 * @see         http://www.asciitable.com/
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\SecretText;

class Ascii extends AbstractSecretText 
{
    /**
     * Encode of original text
     */
    const FROM_ENCODE = 'auto';
    
    /**
     * Encode of prepeared text
     */
    const TO_ENCODE = 'ASCII';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $options = array(
        'text' => ''
    );
    
    /**
     * @param array $options
     */
    public function __construct(array $options = array()) 
    {
        $this->setOptions($options);
        $this->setEncodedText($this->options['text']);
    }

    /**
     * Gets converted data to binary format
     * 
     * @return string binary representation of secret data
     */
    public function getBinaryData() 
    {       
        // convert to binary string 
        $format     = '%0' . self::TEXT_ITEM_LENGTH . 'd';
        $result     = preg_replace_callback(
            '/.{1}|\n{1}/',
            function($match) use($format) { 
                return self::convertData($match[0], function($data) use ($format) {
                    return sprintf($format, decbin(ord($data)));
                }); 
            },
            $this->encodedText
        );
                      
        // add end text mark
        $result .= self::END_TEXT_MARK;
                    
        return $result;        
    }
            
    /**
     * Gets decretText from binary data
     * 
     * @param string    $binaryData - raw secretText with endMark
     * @param integer   $endMarkPos - position of endMark
     * @return string
     */
    public function getFromBinaryData($binaryData, $endMarkPos) 
    {
        // remove endText mark
        $dataFiltered = self::removeEndMark($binaryData, $endMarkPos);
        
        // convert ascii binary code to char
        return self::convertBinaryToChar($dataFiltered);
    }
    
    /**
     * Convert binary data to char
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
                return self::convertData($match[0], function($data){
                    return chr(bindec($data));
                }); 
            }, 
            $binaryData
        );

        return $result;
    }
    
    /**
     * Convert data
     * 
     * @param string $data
     * @param \Closure $converter
     * @return string
     */
    static protected function convertData($data, \Closure $converter)
    {
        $result = self::getFromCache($data);
        if ($result === false) {
           $result = $converter($data);
           self::setToCache($data, $result);
        }

       return $result; 
    }
    
    /**
     * Sets encode text
     * 
     * @param string $encodeText
     */
    protected function setEncodedText($encodeText) 
    {
        $this->encodedText = mb_convert_encoding($encodeText, self::TO_ENCODE, self::FROM_ENCODE);
    }
}
