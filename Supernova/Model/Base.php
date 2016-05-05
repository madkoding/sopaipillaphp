<?php

namespace Supernova\Model;

trait Base
{
    /**
     * Results from database
     * @var array
     */
    protected $results = array();
    
    /**
     * Get the number of items into the results array
     * @return int Number of items
     */
    public function getCount()
    {
        return count($this->results);
    }
    
    /**
     * Get the value form the column of results
     * @param  string $columnName Camelized column name
     * @return mixed  Returns value or false
     */
    private function get($columnName)
    {
        $valueCheck = \Supernova\Inflector\Underscore::word($columnName);
        if (isset($this->results[$valueCheck])) {
            return $this->results[$valueCheck];
        } elseif ($found = $this->findRelation($columnName)) {
            return $found;
        }
        trigger_error(__("Can't get value, column not exist").": ".$valueCheck, E_USER_ERROR);
        return false;
    }

    /**
     * Set values into the results object
     * @param string $column Column name
     * @param string $values  Values
     */
    private function set($column, $values = null)
    {
        if ($values) {
            $column = \Supernova\Inflector\Underscore::word($column);
            $this->results[$column] = current($values);
        }
    }
    
    /**
     * Get the first element of the results array
     * @return array Return the first element
     */
    public function getFirst()
    {
        if (isset($this->results[0])) {
            return $this->results[0];
        }
        return null;
    }

    /**
     * Check if results are empty
     * @return boolean Returns true or false
     */
    private function notEmpty()
    {
        return (count($this->results) > 0) ? false : true;
    }

    /**
     * Save one result from the database
     * @return boolean Returns true or false
     */
    private function save()
    {
        return \Supernova\Sql\Save::result($this);
    }

    /**
     * Remove one result from the database
     * @return boolean Returns true or false
     */
    private function remove()
    {
        return \Supernova\Sql\Remove::result($this);
    }

    /**
     * Goes to the validation class and check the form
     * @return boolean Returns true or false
     */
    private function validate()
    {
        // TODO
        return true;
    }

    /**
     * Check if save process was valid and set a message for the View
     * @return boolean Returns true or false
     */
    private function isValid()
    {
        if ($this->save()) {
            \Supernova\Controller::flash(array("message" => __("Save successful"), "status" => "success"));
            return true;
        } else {
            \Supernova\Controller::flash(array("message" => __("Save failed"), "status" => "danger"));
            return false;
        }
    }

    /**
     * Show error message if validation in proccess form fails
     * @return boolean Always return false
     */
    private function onError()
    {
        \Supernova\Controller::flash(array("message" => __("Validation error"), "status" => "danger"));
        return false;
    }

    /**
     * Convert results object into array
     * @return array Array with results
     */
    public function toArray()
    {
        return $this->results;
    }

    public function getArray()
    {
        $tmpArray = [];
        foreach ($this->results as $result) {
            if (is_object($result)) {
                $tmpArray[] = $result->results;
            }
        }
        return $tmpArray;
    }

    /**
     * Insert an array into the results of an object
     * @param  array  $array Array with data for the results
     * @return null
     */
    public function fromArray($array = array())
    {
        foreach ($array as $k => $v) {
            $this->results[$k] = $v;
        }
    }
}
