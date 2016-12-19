<?php
/**
 * Image iterator with natural order
 * ex. ['x' => 0, 'y' => 0], ['x' => 1, 'y' => 0], ['x' => 2, 'y' => 0], ...
 * 
 * @link        https://github.com/picamator/SteganographyKit
 * @license     http://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace Picamator\SteganographyKit\Iterator;
use Picamator\SteganographyKit\Image\ImageInterface;

class ImageIterator implements \Iterator
{
    /**
     * Image
     * 
     * @var Resource 
     */
    protected $image;
    
    /**
     * Max value for X coordinate
     * 
     * @var integer 
     */
    protected $xMax;
    
    /**
     * Max value for Y coordinate
     *
     * @var integer 
     */
    protected $yMax;
    
    /**
     * Current X coordinate
     * 
     * @var integer 
     */
    protected $x = 0;
    
    /**
     * Current Y coordinate
     * 
     * @var integer 
     */
    protected $y = 0;
    
    /**
     * Current index
     * 
     * @var integer 
     */
    protected $index = 0;
    
    /**
     * @param ImageInterface $image
     */
    public function __construct(ImageInterface $image) 
    {
        $this->image = $image->getImage();
        
        // init max limit
        $imgSize    = $image->getSize();
        $this->xMax = $imgSize['width'];
        $this->yMax = $imgSize['height'];
    }
    
    /**
     * Return the current element
     * 
     * @return array
     */
    public function current() 
    {
        $color = imagecolorat($this->image, $this->x, $this->y);
        
        return array('x' => $this->x, 'y' => $this->y, 'color' => $color);
    }

    /**
     * Return the key of the current element
     * 
     * @return scalar scalar on success, or null on failure
     */
    public function key() 
    {   
        return $this->index;
    }
    
    /**
     * Move forward to next element
     * 
     * @return void Any returned value is ignored
     */
    public function next() 
    {
        if ($this->x + 1 >= $this->xMax) {
            $this->x = 0;
            $this->y ++;
        } else {
            $this->x ++;
        }
        
        $this->index ++;
    }

    /**
     * Rewind the Iterator to the first element
     * 
     * @return void Any returned value is ignored.
     */
    public function rewind() 
    {
        $this->x        = 0;
        $this->y        = 0;
        $this->index    = 0;
    }

    /**
     * Checks if current position is valid
     * 
     * @return boolean true on success or false on failure
     */
    public function valid()
    {     
        $xValid = $this->x < $this->xMax;
        $yValid = $this->y < $this->yMax;
        
        return  $xValid && $yValid;
    }
}
