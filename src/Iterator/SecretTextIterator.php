<?php
namespace Picamator\SteganographyKit\Iterator;

use Picamator\SteganographyKit\SecretText\SecretTextInterface;

/**
 * Secret Text Binary Iterator
 */
class SecretTextIterator implements \Iterator
{
    /**
     * Binary data
     * 
     * @var string 
     */
    protected $data;
    
    /**
     * Max Index
     * 
     * @var int
     */
    protected $maxIndex;
    
    /**
     * Size of bits in one item
     * 
     * @var int
     */
    protected $itemSize;
    
    /**
     * Current index
     * 
     * @var int
     */
    protected $index = 0;
    
    /**
     * @param SecretTextInterface $secretText
     */
    public function __construct(SecretTextInterface $secretText) 
    {
        $this->data     = $secretText->getBinaryData();
        $this->itemSize = $secretText->getBinaryItemSize();
        
        $this->maxIndex = strlen($this->data) - 1;
    }
    
    /**
     * Return the current element
     * 
     * @return array
     */
    public function current() 
    {
        return substr($this->data, $this->index, $this->itemSize);
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
        $this->index += $this->itemSize;
    }

    /**
     * Rewind the Iterator to the first element
     * 
     * @return void Any returned value is ignored.
     */
    public function rewind() 
    {
        $this->index = 0;
    }

    /**
     * Checks if current position is valid
     * 
     * @return bool true on success or false on failure
     */
    public function valid()
    {
        return  $this->index <= $this->maxIndex;
    }
}
