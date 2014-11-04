<?php
/**
 * Stego Pseudo-Random Key
 * Gets coordinats as a  element of pseudo-random sequences where seed is a secretKey
 * It's used function mt_srand (Mersenne Twister implementation in PHP)
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace Picamator\SteganographyKit\StegoKey;
use Picamator\SteganographyKit\InvalidArgumentException;

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
     * @param string|integer $secretKey
     * @return self
     * @throw SteganographyKit\InvalidArgumentException
     */
    public function setSecretKey($secretKey)
    {
        if ($this->validateSecretKey($secretKey) === false) {
            throw new InvalidArgumentException('Invalid secretKey: "' . $secretKey . '"');
        }
        
        $this->secretKey = $secretKey;
        
        return $this;
    }
    
    /**
     * Generate secretKey
     * 
     * @param boolean $autoSet - true auto set property secretKey, false only return value
     * @return string|integer
     */
    public function generateSecretKey($autoSet = false)
    {
        $min = pow(10, self::MAX_SECRET_KEY_LENGTH - 1);
        $max = (int) str_replace(array(1, 0), 9, $min);
        
        $secretKey = mt_rand($min, $max);        
        if ($autoSet === true) {
            $this->setSecretKey($secretKey);
        }
        
        return $secretKey;
    }
        
    /**
     * Validate SecretKey
     * 
     * @param string|integer $secretKey
     * @return boolean - true for ok or false otherwise
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
