<?php
/**
 * Abstract for Secret Text
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\SecretText;
use SteganographyKit\Options\OptionsTrait;

abstract class AbstractSecretText implements SecretTextInterface, \Countable, \IteratorAggregate 
{    
    use OptionsTrait;
    
    /**
     * Mark that is added to end of the text
     * it helps to identify where secret text end
     */
    const END_TEXT_MARK = '0000000000000000';
       
    /**
     * Length of secretText item in binary
     */
    const BINARY_ITEM_LENGTH = 8;
    
    /**
     * Data Options
     * 
     * @var array 
     */
    protected $dataOptions = array();
    
    /**
     * Binary Item Size
     * 
     * @var integer 
     */
    protected $binaryItemSize = 3;
    
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
     * Sets binary item size
     * It's used for iteration process
     * 
     * @param integer $size
     * @return self
     */
    public function setBinaryItemSize($size) 
    {
        $this->binaryItemSize = $size;
        
        return $this;
    }
    
    /**
     * Gets binary item size
     * It's used for iteration process
     * 
     * @return integer
     */
    public function getBinaryItemSize() 
    {
        return $this->binaryItemSize;
    }
    
    /**
     * Add end mark
     * 
     * @param string $secretText
     * @return string
     */
    protected function addEndMark($secretText) 
    {
        return $secretText . self::END_TEXT_MARK;
    }
    
    /**
     * Remove end mark
     * 
     * @param string $secretText
     * @return string
     */
    protected function removeEndMark($secretText) 
    {
        $endMarkPos  = $this->getEndMarkPos($secretText);
        $result      = substr($secretText, 0, $endMarkPos);
        
        // when last characters has last bits zeros then it could be a part of EndMark
        // therefore it's possible to remove bits from last character
        $repeat  = strlen($result) % self::BINARY_ITEM_LENGTH;
        if ($repeat !== 0) {
            $result .= str_repeat('0', self::BINARY_ITEM_LENGTH - $repeat);
        }
        
        return $result;
    }
}