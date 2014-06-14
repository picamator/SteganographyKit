<?php
/**
 * Abstract for Stego Text
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\StegoText;
use SteganographyKit\Options\OptionsTrait;

abstract class AbstractStegoText implements StegoTextInterface 
{
    use OptionsTrait;
    
    /**
     * Image size
     * 
     * @var array
     * <code>
     *      array(6) {
     *        'width' =>
     *         int(3264)
     *         'height' =>
     *         int(2448)
     *         'type' =>
     *         int(3)
     *         'attr' =>
     *         string(26) "width="3264" height="2448""
     *         'bits' =>
     *         int(8)
     *         'mime' =>
     *         string(9) "image/png"
     *       }
     * </code>
     */
    protected $imgSize;
    
    /**
     * Supported Mime format
     * 
     * @var string
     */
    protected $supportedMime;
    
    /**
     * @param array $options
     */
    public function __construct(array $options) 
    {
        $this->validateGbLib();
        $this->validateOptions($options);
        
        $this->setOptions($options);
        
        $this->setImgSize();
        $this->validateMime();
    }
    
    /**
     * Gets size of suppose Embedded data
     * 
     * @return integer
     */
    public function getSecretTextSize() 
    {
        // it's supposed that img has 3 channels RGB
        return $this->imgSize['width'] * $this->imgSize['height'] * 3;
    }
    
    /**
     * Verify is GB Lib extension was loaded
     * 
     * @throws Exception
     */
    protected function validateGbLib() 
    {
        if(extension_loaded('gd') === false) {
            throw new Exception('GD php extension was not loaded: http://www.php.net/manual/en/book.image.php');
        } 
    }
    
    /**
     * Validate options
     * 
     * @param array $options
     * @throws Exception
     */
    protected function validateOptions(array $options) {
        if(!isset($options['path'])
            || !file_exists($options['path']) 
            || !is_readable($options['path'])
        ) {    
            throw new Exception('Image is not exsit or not readable.');
        }
    }
    
    /**
     * Sets image size
     */
    protected function setImgSize() 
    {
        $result = getimagesize($this->options['path']);
        if($result === false) {
            throw new Exception('Imposible to calculate image size: '.$this->options['path']);
        }
        
        $this->imgSize = array_combine(
            array('width', 'height', 'type', 'attr', 'bits', 'mime'),
            $result     
        );
    }
    
    /**
     * Validate supported mime type
     */
    protected function validateMime() 
    {
        if ($this->imgSize['mime'] !== $this->supportedMime) {
            throw new Exception('Mime "' . $this->imgSize['mime'] . '" is not supported.');
        }
    }
}