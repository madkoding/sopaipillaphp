<?php

namespace Supernova\Model;

trait Magic
{
    /**
     * Magic setter for the results from the database
     * @param string $name  Column name
     * @param string $value Value
     */
    public function __set($name, $value)
    {
        $this->results[$name] = html_entity_decode(htmlentities($value), ENT_QUOTES, "ISO-8859-1");
    }

    /**
     * Show the default value column if do a echo to the result object
     * @return string Shows default value column
     */
    public function __toString()
    {
        if (isset($this->defaultKey)) {
            if (isset($this->results[$this->defaultKey])) {
                return $this->results[$this->defaultKey];
            }
        }
        return "";
    }

    /**
     * Magic call to the model functions
     * @param  string $name      Function name
     * @param  array  $arguments Arguments
     * @return function
     */
    public function __call($name, $arguments = array())
    {
        $functionPrefix = substr($name, 0, 3);
        $functionName = substr($name, 3);
        if (method_exists($this, $functionPrefix.$functionName)) {
            return call_user_func_array(array($this, $name), $arguments);
        } else {
            switch ($functionPrefix) {
                case 'get':
                    return $this->get(substr($name, 3));
                    break;
                case 'set':
                    $this->set(substr($name, 3), $arguments);
                    break;
                default:
                    return call_user_func_array(array($this, $name), $arguments);
                    break;
            }
        }
    }
    
    /**
     * Call SQL functions from model
     * @param  string $name      Function name
     * @param  array  $arguments Array with arguments
     * @return function            Return SQL function
     */
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array(array("\Supernova\Sql\Query", $name), $arguments);
    }
}
