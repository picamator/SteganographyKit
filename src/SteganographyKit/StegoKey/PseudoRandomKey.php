<?php
/**
 * Stego Pseudo-Random Key
 * Gets coordinats as a  element of pseudo-random sequences where seed is a secretKey
 * It's used function mt_srand (Mersenne Twister implementation in PHP)
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoKey;

class PseudoRandomKey extends AbstractStegoKey
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
     * Max number of repeat coordinate for for trying get new one
     */
    const MAX_COORDINAT_REPEAT = 20;
    
    /**
     * Container of coordinats that have been generated before
     * 
     * @var array 
     */
    protected $coordinats = array();
    
    /**
     * Sets secretKey
     * 
     * @param string|integer $secretKey
     * @return self
     * @throw Exception
     */
    public function setSecretKey($secretKey)
    {
        if ($this->validateSecretKey($secretKey) === false) {
            throw new Exception('Invalid secretKey: "' . $secretKey . '"');
        }
        
        $this->secretKey    = $secretKey;
        // reset coordinats container
        $this->coordinats   = array();
        
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
     * Geta coordinats
     * 
     * @param integer $xMax
     * @param integer $yMax
     * @return array - array('x' => 10, 'y' => 5)
     * @throw Exception
     */
    public function getCoordinats($xMax, $yMax)
    {      
        // set seed
        $secretKey = $this->getSecretKey();
        mt_srand($secretKey);
        
        $result = false;
        $i      = 0;
        while ($i < self::MAX_COORDINAT_REPEAT && $result === false) {
            $x = mt_rand(0, $xMax);
            $y = mt_rand(0, $yMax);
            
            // check if it's new one
            if(!key_exists($x, $this->coordinats) 
                || !in_array($y, $this->coordinats[$x])
            ) {
                $result = array('x' => $x,'y' => $y);
                $this->coordinats[$x][] = $y;
            }
            
            $i++;
        }
         
        if ($result === false) {
            throw new Exception('Coordinat generation was failed. Attempted ' 
                . self::MAX_COORDINAT_REPEAT . ' times to get new one was used.');
        }
        
        return $result;
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
        $result =  $length >= self::MIN_SECRET_KEY_LENGTH && $length <= self::MAX_SECRET_KEY_LENGTH;
     
        return $result;
    }
}

