<?php
/**
 * Interface for Secret Text
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\SecretText;

interface SecretTextInterface 
{    
    /**
     * @param array $options
     */
    public function __construct(array $options = array());
    
    /**
     * Sets binary item size
     * It's used for iteration process
     * 
     * @param integer $size
     * @return self
     */
    public function setBinaryItemSize($size);
    
    /**
     * Gets binary item size
     * It's used for iteration process
     * 
     * @return integer
     */
    public function getBinaryItemSize();
    
    /**
     * Gets converted data to binary format
     * 
     * @param array $dataOptions - contains data or path to data
     * @return string - binary representation of secret data
     */
    public function getBinaryData();
    
    /**
     * Gets decretText from binary data
     * 
     * @param string    $binaryData - raw secretText with endMark
     * @return string
     */
    public function getFromBinaryData($binaryData);
    
    /** 
     * Gets position of end mark
     * 
     * @param string $secretText
     * @return integer|false
     */
    public function getEndMarkPos($secretText);
}
