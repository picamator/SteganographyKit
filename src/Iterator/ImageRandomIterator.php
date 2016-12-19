<?php
namespace Picamator\SteganographyKit\Iterator;

use Picamator\SteganographyKit\Image\ImageInterface;

/**
 * Image iterator with random order
 */
class ImageRandomIterator implements \Iterator
{   
    /**
     * Image
     * 
     * @var resource 
     */
    protected $image;
      
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
     * Max index of x coordinate
     * 
     * @var integer 
     */
    protected $xMax;
    
    /**
     * Max index of y coordinate
     * 
     * @var integer 
     */
    protected $yMax;
    
    /**
     * Current index
     * 
     * @var integer 
     */
    protected $index = 0;
    
    /**
     * Max index
     * 
     * @var integer 
     */
    protected $indexMax;
    
    /**
     * Image size
     * 
     * @var array  
     */
    protected $imageSize;
    
    /**
     * Container of coordinats that have been generated before
     * 
     * @var array 
     */
    protected $coordinates;
    
    /**
     * Random seed
     * 
     * @var string 
     */
    protected $randSeed;
    
    /**
     * @param ImageInterface $image
     * @param string $randSeed 
     */
    public function __construct(ImageInterface $image, $randSeed) 
    {
        $this->image    = $image->getImage();
        $this->randSeed = $randSeed;
        
        $this->imgSize = $image->getSize();
        
        $this->setMaxLimit();
        $this->setCoordinates();
         
        // init rand
        $this->initRand();
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
        // generate xy
        $x = $this->getRand(0, $this->xMax);
        $y = $this->getRand(0, $this->yMax);
        
        if ($this->coordinates[$x][$y] === 1) {
            list($x, $y) = $this->getNextCoordinate();
        }
        $this->coordinates[$x][$y] = 1;
        
        $this->x = $x;
        $this->y = $y;
        
        $this->index ++;
    }

    /**
     * Rewind the Iterator to the first element
     * 
     * @return void Any returned value is ignored.
     */
    public function rewind() 
    {
        $this->x            = 0;
        $this->y            = 0;
        $this->index        = 0;
        $this->setCoordinates();
        $this->initRand();
    }

    /**
     * Checks if current position is valid
     * 
     * @return boolean true on success or false on failure
     */
    public function valid()
    {             
        return  $this->index < $this->indexMax;
    }
    
    /**
     * Gets random data
     * 
     * @param integer $min
     * @param integer $max
     * @return integer
     */
    protected function getRand($min, $max) 
    {
        return mt_rand($min, $max);
    }
    
    /**
     * Init random generator
     */
    protected function initRand()
    {
        // set seed to random generator
        mt_srand($this->randSeed);
    }
    
    /**
     * Sets MaxLimit
     */
    protected function setMaxLimit()
    {       
        $this->xMax =  $this->imgSize['width'] - 1;
        $this->yMax =  $this->imgSize['height'] - 1;
        
        $this->indexMax = $this->imgSize['width'] * $this->imgSize['height'];
    }
    
    /**
     * Sets coordinates
     * 
     * @param array $imgSize
     */
    protected function setCoordinates() 
    {        
        $yList              = array_fill(0,  $this->imgSize['height'], 0);
        $coordinates        = array_fill(0, $this->imgSize['width'], $yList);
        $coordinates[0][0]  = 1;
        
        $this->coordinates = $coordinates;
    }
    
    protected function getNextCoordinate()
    {
        $x = 0;
        $y = 0;
        foreach($this->coordinates as $key => $value) {
            $x = $key;
            $y = array_search(0, $value); 
            if ($y !== false) {
                break;
            }
        }
        
        return array($x, $y);
    }
}
