<?php
/**
 * Secret Text - Plain text
 * 
 * @see         http://www.asciitable.com/
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\SecretText;
use SteganographyKit\Iterator\SecretTextIterator;
use SteganographyKit\RuntimeException;
use SteganographyKit\InvalidArgumentException;

class PlainText extends AbstractSecretText 
{   
    /**
     * Length of secretText item in binary
     */
    const BINARY_ITEM_LENGTH = 8;
    
    /**
     * Options
     * 
     * @var array
     */
    protected $options = array(
        'compressLevel' => -1
    );
      
    /**
     * Data Options
     * 
     * @var array 
     */
    protected $dataOptions = array(
        'text' => ''
    );
    
    /**
     * Cache container
     * 
     * @var array 
     */
    static protected $cache = array();
    
    /**
     * @param array $options
     */
    public function __construct(array $options = array()) 
    {
        parent::__construct($options);
        
        $this->validateZLib();
    }
    
   /**
    * Gets Iterator
    * 
    * @return SecretTextIterator
    */ 
    public function getIterator() 
    {
        return new SecretTextIterator($this);
    }

    /**
     * Gets converted data to binary format
     * 
     * @return string - binary representation of secret data
     * @throws SteganographyKit\InvalidArgumentException
     */
    public function getBinaryData()
    {       
        $dataOptions = $this->getDataOptions();
        
        // encode
        $encode         = $this->encode($dataOptions['text']);
        $encodeLength   = strlen($encode);
  
        $format     = '%0' . self::BINARY_ITEM_LENGTH . 'd';
        $converter  = function($data) use ($format) {
            return sprintf($format, decbin(ord($data)));
        };
        
        $result = '';
        for ($i = 0; $i < $encodeLength; $i ++) {
            $result .= self::convertData($encode[$i], $converter);
        }
                       
        // add end text mark
        $result .= self::END_TEXT_MARK;
                    
        return $result;        
    }
            
    /**
     * Gets decretText from binary data
     * 
     * @param string    $binaryData - raw secretText with endMark
     * @return string
     */
    public function getFromBinaryData($binaryData) 
    {
        $binaryLength   = strlen($binaryData);
        $converter      = function($data) {
            return chr(bindec($data));
        };
        
        $text = '';
        for ($i = 0; $i < $binaryLength; $i = $i + self::BINARY_ITEM_LENGTH) {
            $binaryItem  = substr($binaryData, $i, self::BINARY_ITEM_LENGTH);
            $text       .= self::convertData($binaryItem, $converter);
        }
                
        // decode
        $result = $this->decode($text);
        
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
    
    /**
     * Validate compress library
     * 
     * @throws SteganographyKit\RuntimeException
     */
    protected function validateZLib()
    {
        if (!function_exists('gzcompress')) {
            throw new RuntimeException('ZLib was not installed: http://php.net/manual/en/book.zlib.php');
        }
    }
    
    /**
     * Encode text
     * 
     * @param string $text
     * @return string - base64encoded compresses string
     */
    protected function encode($text) 
    {
        return base64_encode(gzcompress($text, $this->options['compressLevel']));
    }
    
    /**
     * Decode text
     * 
     * @param string $text
     * @return string
     */
    protected function decode($text) 
    {
        return gzuncompress(base64_decode($text));
    }
    
    /**
     * Gets DataOptions
     * 
     * @return array
     * @throws SteganographyKit\InvalidArgumentException
     */
    protected function getDataOptions() 
    {
        if (empty($this->dataOptions['text'])) {
            throw new InvalidArgumentException('Text was not set in Data Options');
        }
        
        return $this->dataOptions;
    }
}
