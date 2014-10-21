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
     * Encode that was suppose to be an original text
     */
    const FROM_ENCODE = 'auto';
        
    /**
     * Options
     * 
     * @var array
     */
    protected $options = array(
        'text' => ''
    );
    
    /**
     * Encoded text to ASCII
     *
     * @var string 
     */
    protected $encodedText;
        
    /**
     * @param array $options
     */
    public function __construct(array $options) 
    {
        $this->setOptions($options);
        $this->setEncodedText();
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
                return sprintf($format, decbin(ord($match[0])));
            },
            $this->encodedText
        );
                      
        // add end text mark
        $result .= self::END_TEXT_MARK;
                    
        return $result;        
    }
            
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
     * Sets encode text
     */
    protected function setEncodedText() 
    {
        $this->encodedText = mb_convert_encoding(
            $this->options['text'], 
            'ASCII', 
            self::FROM_ENCODE
        );
    }
}
