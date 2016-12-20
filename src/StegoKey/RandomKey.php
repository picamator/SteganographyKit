<?php
namespace Picamator\SteganographyKit\StegoKey;

use Picamator\SteganographyKit\InvalidArgumentException;

/**
 * Stego Pseudo-Random Key
 * Gets coordinates as a  element of pseudo-random sequences where seed is a secretKey
 * It's used function mt_srand (Mersenne Twister implementation in PHP)
 */
class RandomKey extends AbstractStegoKey
{ 
    /**
     * Min length of secretKey
     */
    const MIN_SECRET_KEY_LENGTH = 4;
    
    /**
     * Max length of secretKey
     */
    const MAX_SECRET_KEY_LENGTH = 8;
        
    /**
     * Sets secretKey
     * 
     * @param string | int $secretKey
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function setSecretKey($secretKey)
    {
        if ($this->validateSecretKey($secretKey) === false) {
            throw new InvalidArgumentException(
                sprintf('Invalid secretKey "%s"', $secretKey)
            );
        }
        
        $this->secretKey = $secretKey;
        
        return $this;
    }
    
    /**
     * Generate secretKey
     * 
     * @param bool $autoSet - true auto set property secretKey, false only return value
     *
     * @return string | int
     */
    public function generateSecretKey($autoSet = false)
    {
        $min = pow(10, self::MAX_SECRET_KEY_LENGTH - 1);
        $max = (int) str_replace([1, 0], 9, $min);
        
        $secretKey = mt_rand($min, $max);        
        if ($autoSet === true) {
            $this->setSecretKey($secretKey);
        }
        
        return $secretKey;
    }
        
    /**
     * Validate SecretKey
     * 
     * @param string | int $secretKey
     *
     * @return bool - true for ok or false otherwise
     */
    protected function validateSecretKey($secretKey) 
    {
        if (!is_int($secretKey)) {
            return false;
        }
        
        $length = strlen($secretKey);
        $result = $length >= self::MIN_SECRET_KEY_LENGTH && $length <= self::MAX_SECRET_KEY_LENGTH;
     
        return $result;
    }
}
