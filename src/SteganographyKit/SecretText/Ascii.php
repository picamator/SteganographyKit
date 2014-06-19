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
     * @return array each element has 7 bit
     */
    public function getBinaryData() 
    {       
        $textSplit  = \str_split($this->encodedText);     
        $result     = array();
        foreach ($textSplit as $item) {
            $item       = decbin(ord($item));
            $item       = str_pad($item, 7, '0', STR_PAD_LEFT);
            $result[]   = $item;
        }
        
        // add end text mark
        $result[] = self::END_TEXT_MARK;
                
        return $result;        
    }
         
    /**
     * Gets size data in bit
     * 
     * @return integer
     */
    public function getSize() 
    {
        return strlen($this->encodedText) * 7 + strlen(self::END_TEXT_MARK);
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
