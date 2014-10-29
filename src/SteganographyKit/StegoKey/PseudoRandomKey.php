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
    const MAX_COORDINAT_REPEAT = 10;
    
    /**
     * Container of coordinats that have been generated before
     * 
     * @var array 
     */
    protected $coordinates = array();
    
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
        
        $secretKey = self::generateRandom($min, $max);        
        if ($autoSet === true) {
            $this->setSecretKey($secretKey);
        }
        
        return $secretKey;
    }
    
    /**
     * Geta coordinate
     * 
     * @param array   $prevCoordinate
     * @param integer $xMax
     * @param integer $yMax
     * @return array - array('x' => 10, 'y' => 5)
     * @throw Exception
     * @FIXME It's possible that secretkey was not set
     */
    public function getCoordinate(array $prevCoordinate, $xMax, $yMax)
    {   
        // reset coordinate
        if ($prevCoordinate['x'] === 0 && $prevCoordinate['y'] === 0) {
            $this->resetCoordinate();           
        }
        
        // generate
        $result = null;
        $i      = 0;
        while (is_null($result) && $i < self::MAX_COORDINAT_REPEAT) {
            $x = self::generateRandom(0, $xMax);
            $y = self::generateRandom(0, $yMax);
            
            if(self::validateCoordinate($x, $y) === true) {
                $result = array('x' => $x, 'y' => $y);    
            }
            $i++;
        }
         
        if (is_null($result)) {
            throw new Exception('Coordinate generation was failed. The ' 
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
    static protected function validateSecretKey($secretKey) 
    {
        if (!is_int($secretKey)) {
            return false;
        }
        
        $length = strlen($secretKey);
        $result = $length >= self::MIN_SECRET_KEY_LENGTH && $length <= self::MAX_SECRET_KEY_LENGTH;
     
        return $result;
    }
    
    /**
     * Generate random data
     * 
     * @param integer $min
     * @param integer $max
     * @return integer
     * @see PseudoRandomKeyTest::testRandomImg
     */
    static protected function generateRandom($min, $max) 
    {
        return mt_rand($min, $max);
    }
    
    /**
     * Validate coordinate
     * 
     * @param integer $x
     * @param integer $y
     * @return boolean - true if vaild false otherwise
     */
    protected function validateCoordinate($x, $y) 
    {
        if (key_exists($x, $this->coordinates) && in_array($y, $this->coordinates[$x])) {
            return false;
        } 
        $this->coordinates[$x][] = $y;
        
        return true;
    }
    
    /**
     * Reset pseudo-random coordinate sequence
     */
    protected function resetCoordinate()
    {
        $secretKey = $this->getSecretKey();
        
        // reset coordinats container
        $this->coordinates   = array();
        
        // set seet to random generator
        mt_srand($secretKey);
    }
}
