<?php
namespace Picamator\SteganographyKit\StegoKey;

/**
 * Stego Key
 */
interface StegoKeyInterface 
{
    /**
     * @param string|int $secretText
     */
    public function __construct($secretText = null);
    
    /**
     * Sets secretKey
     * 
     * @param string | int $secretKey
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function setSecretKey($secretKey);
    
    /**
     * Gets secretKey
     * 
     * @return string | int
     *
     * @throws LogicException
     */
    public function getSecretKey();
    
    /**
     * Generate secretKey
     * 
     * @param bool $autoSet - true auto set property secretKey, false only return value
     *
     * @return string | int
     */
    public function generateSecretKey($autoSet = false);
}
