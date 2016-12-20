<?php
namespace Picamator\SteganographyKit\StegoKey;

use Picamator\SteganographyKit\LogicException;

/**
 * Abstract for Stego Key
 */
abstract class AbstractStegoKey implements StegoKeyInterface 
{
    /**
     * SecretKey
     * 
     * @var string|int
     */
    protected $secretKey;
    
    /**
     * @param string|int $secretText
     */
    public function __construct($secretText = null) 
    {
        if (!is_null($secretText)) {
            $this->setSecretKey($secretText);
        }
    }
    
    /**
     * Gets secretKey
     * 
     * @return string|int
     *
     * @throws LogicException
     */
    public function getSecretKey() 
    {
        if (is_null($this->secretKey)) {
            throw new LogicException('SecretKey was not set');
        }
        
        return $this->secretKey;
    }
}
