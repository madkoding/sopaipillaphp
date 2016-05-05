<?php

namespace Supernova;

class Console
{
    /**
     * Settings to inject in PHP
     * @var array
     */
    private static $settings = array(
        "ini_set" => array("zlib.output_compression", 0),
        "ini_set" => array("output_buffering", 0),
        "ini_set" => array("implicit_flush", 1),
        "set_time_limit" => array(0)
    );
    
    /**
     * Set values from settings to enable console output in browser
     * @return function
     */
    private static function initialize()
    {
        foreach (self::$settings as $functionName => $value) {
            call_user_func_array($functionName, $value);
        }
    }
    
    /**
     * Start console session
     * @return null
     */
    public static function start()
    {
        self::initialize();
        ob_end_clean();
        ob_implicit_flush(true);
        header('Content-type: text/html; charset=utf-8');
    }

    /**
     * Set message in console session
     * @param  string $message Message
     * @return null
     */
    public static function log($message = "")
    {
        echo $message;
        flush();
    }
}
