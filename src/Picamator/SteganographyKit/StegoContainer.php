<?php
/**
 * SteganographyKit Container
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace Picamator\SteganographyKit;
use Picamator\SteganographyKit\StegoSystem\StegoSystemInterface;
use Picamator\SteganographyKit\SecretText\PlainText;
use Picamator\SteganographyKit\Image\Image;

class StegoContainer
{
    /**
     * Default Stego System
     */
    const DEFAULT_STEGO_SYSTEM = 'Picamator\SteganographyKit\StegoSystem\PureLsb';
    
    /**
     * StegoSystem
     * 
     * @var StegoSystemInterface 
     */
    protected $stegoSystem = null;    
    
    /**
     * Image
     * 
     * @var Image 
     */
    protected $image;
    
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
        $this->setImage(array(
            'path'      => $coverPath,
            'savePath'  => $stegoPath
        ));
        $secretText = new PlainText(array(
            'text' => $text
        ));
        
        return $this->getStegoSystem()
            ->encode($secretText, $this->image);
    }
    
    /**
     * Decode
     * 
     * @param string $stegoPath
     * @retun string
     */
    public function decode($stegoPath) 
    {
        $this->setImage(array(
            'path'=> $stegoPath,
        ));  
        $secretText = new PlainText();
        
        return $this->getStegoSystem()
            ->decode($this->image, $secretText);
    }
    
    /**
     * Render image
     * raw image stream will be outputted directly
     */
    public function renderImage() 
    {
        imagepng($this->image->getImage());
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
    
    /**
     * Sets image
     * 
     * @param array $options
     */
    protected function setImage(array $options) 
    {
        $this->image = new Image($options);
    }
}
