<?php

namespace Supernova\Model;

trait Iterator
{
    /**
     * Back one position from array results
     * @return null
     */
    public function rewind()
    {
        reset($this->results);
    }
    
    /**
     * Get current result from array
     * @return mixed
     */
    public function current()
    {
        return current($this->results);
    }
    
    /**
     * Get key result from array
     * @return mixed
     */
    public function key()
    {
        return key($this->results);
    }
    
    /**
     * Advance to the next item in the array
     * @return null
     */
    public function next()
    {
        next($this->results);
    }
    
    /**
     * Check if key form array is valid
     * @return boolean Return true or false
     */
    public function valid()
    {
        return $this->key() !== null;
    }
}
