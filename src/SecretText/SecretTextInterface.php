<?php
namespace Picamator\SteganographyKit\SecretText;

/**
 * Interface for Secret Text
 */
interface SecretTextInterface extends \Countable, \IteratorAggregate
{    
    /**
     * @param array $options
     */
    public function __construct(array $options = []);
    
    /**
     * Sets binary item size
     * It's used for iteration process
     * 
     * @param int $size
     *
     * @return self
     */
    public function setBinaryItemSize($size);
    
    /**
     * Gets binary item size
     * It's used for iteration process
     * 
     * @return int
     */
    public function getBinaryItemSize();
    
    /**
     * Gets converted data to binary format
     * 
     * @return string - binary representation of secret data
     */
    public function getBinaryData();
    
    /**
     * Gets decretText from binary data
     * 
     * @param string    $binaryData - raw secretText with endMark
     *
     * @return string
     */
    public function getFromBinaryData($binaryData);
    
    /** 
     * Gets position of end mark
     * 
     * @param string $secretText
     *
     * @return int|false
     */
    public function getEndMarkPos($secretText);
}
