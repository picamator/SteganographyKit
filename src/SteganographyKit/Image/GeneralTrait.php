<?php
/**
 * General image Trait
 * Handle to get binary dat of pixel from png image
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace SteganographyKit\Image;

trait GeneralTrait
{
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
     * Link to image resource
     * 
     * @var resource 
     */
    protected $image;
    
    /**
     * Gets image
     * 
     * @return resource
     */
    public function getImage() 
    {
        return $this->image;
    }
    
    /**
     * Gets image size
     * 
     * @return array
     */
    public function getImageSize()
    {
        return $this->imgSize;
    }        
    
    /**
     * Sets image size
     * 
     * @param string $path
     */
    protected function setImgSize($path) 
    {
        $result = getimagesize($path);
        if($result === false) {
            throw new Exception('Imposible to calculate image size: '.$path);
        }
        
        $this->imgSize = array_combine(
            array('width', 'height', 'type', 'attr', 'bits', 'mime'),
            $result     
        );
        
        // validate mime
        $this->validateMime(); 
    }
    
    /**
     * Verify is GB Lib extension was loaded
     * 
     * @throws Exception
     */
    static protected function validateGbLib() 
    {
        if(extension_loaded('gd') === false) {
            throw new Exception('GD php extension was not loaded: http://www.php.net/manual/en/book.image.php');
        } 
    }
    
    /**
     * Validate supported mime type
     * 
     * @throws Exception
     */
    protected function validateMime() 
    {
        if ($this->imgSize['mime'] !== $this->supportedMime) {
            throw new Exception('Mime "' . $this->imgSize['mime'] . '" is not supported.');
        }
    }
}