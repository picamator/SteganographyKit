<?php
namespace Picamator\SteganographyKit;

use Picamator\SteganographyKit\StegoSystem\StegoSystemInterface;
use Picamator\SteganographyKit\Image\Image;
use Picamator\SteganographyKit\ObjectManager\ObjectManager;

/**
 * Steganography Facade
 */
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
     *
     * @return bool true for success or false otherwise
     */
    public function encode($coverPath, $stegoPath, $text) 
    {
        $this->setImage([
            'path'      => $coverPath,
            'savePath'  => $stegoPath,
        ]);
        $secretText = ObjectManager::getInstance()->create(
            'Picamator\SteganographyKit\SecretText\PlainText',
            [['text' => $text]]
        );
        
        return $this->getStegoSystem()
            ->encode($secretText, $this->image);
    }
    
    /**
     * Decode
     * 
     * @param string $stegoPath
     *
     * @return string
     */
    public function decode($stegoPath) 
    {
        $this->setImage([
            'path'=> $stegoPath,
        ]);
        $secretText = ObjectManager::getInstance()->create('Picamator\SteganographyKit\SecretText\PlainText');
        
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
     *
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
            $this->stegoSystem  = ObjectManager::getInstance()->create($stegoSystem);
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
        $this->image = ObjectManager::getInstance()->create('Picamator\SteganographyKit\Image\Image', [$options]);
    }
}
