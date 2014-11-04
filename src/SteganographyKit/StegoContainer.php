<?php
/**
 * SteganographyKit Container
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit;
use SteganographyKit\StegoSystem\StegoSystemInterface;
use SteganographyKit\SecretText\PlainText;
use SteganographyKit\Image\Image;

class StegoContainer
{
    /**
     * Default Stego System
     */
    const DEFAULT_STEGO_SYSTEM = '\SteganographyKit\StegoSystem\PureLsb';
    
    /**
     * StegoSystem
     * 
     * @var StegoSystemInterface 
     */
    protected $stegoSystem = null;    
    
    /**
     * Encode
     * 
     * @param string $coverPath
     * @param string $stegoPath
     * @param string $text
     * @return boolen true for success or false otherwise
     */
    public function encode($coverPath, $stegoPath, $text) 
    {
        $coverText  = new Image(array(
            'path'      => $coverPath,
            'savePath'  => $stegoPath
        ));  
        $secretText = new PlainText(array(
            'text' => $text
        ));
        
        return $this->getStegoSystem()
            ->encode($secretText, $coverText);
    }
    
    /**
     * Decode
     * 
     * @param string $stegoPath
     * @retun string
     */
    public function decode($stegoPath) 
    {
        $stegoText  = new Image(array(
            'path'=> $stegoPath,
        ));  
        $secretText = new PlainText();
        
        return $this->getStegoSystem()
            ->decode($stegoText, $secretText);
    }
    
    /**
     * Sets Stego System
     * 
     * @param StegoSystemInterface $stegoSystem
     * @return self
     */
    public function setStegoSystem(StegoSystemInterface $stegoSystem)
    {
        $this->stegoSystem = $stegoSystem;
        
        return $this;
    }
    
    /**
     * Gets Stego System
     * 
     * @return self
     */
    protected function getStegoSystem() 
    {
        if (is_null($this->stegoSystem)) {
            $stegoSystem        = self::DEFAULT_STEGO_SYSTEM;
            $this->stegoSystem  = new $stegoSystem();
        }
        
        return  $this->stegoSystem;
    }
}
