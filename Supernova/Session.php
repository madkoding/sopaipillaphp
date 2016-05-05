<?php

namespace Supernova;

class Session
{
    /**
     * Create session
     *
     * @param   string  $key    Key for session
     * @param   string  $value  Value for session
     */
    public static function create($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Destroy session
     * @param   string  $key    Key value to destroy
     */
    public static function destroy($key)
    {
        $_SESSION[$key] = null;
        unset($_SESSION[$key]);
    }

    /**
     * Read session
     * @param   string  $key    Key value to read
     * @return  string      Value for key
     */
    public static function read($key)
    {
        return (isset($_SESSION[$key]) && !empty($_SESSION[$key])) ? $_SESSION[$key] : null ;
    }
}
