<?php

namespace Supernova\Core;

class Config
{
    /**
     * Set static vars to use for core
     * @var array
     */
    public static $vars = array();
    
    /**
     * Settings that will goes into the PHP
     * @var array
     */
    public static $settings = array(
        "ini_set" => array("default_charset", "utf-8"),
        "ini_set" => array("session.gc_probability", 0),
        "ini_set" => array("error_reporting", 0),
        "ini_set" => array("display_errors", 'Off'),
        "ini_set" => array("display_startup_errors", 'Off'),
        "ini_set" => array("log_errors", 'Off')
    );

    public static function setErrorHandler()
    {
        $settings = array(
            "register_shutdown_function" => array("shutdownFunction"),
            "set_error_handler" => array("errorHandler"),
        );
        foreach ($settings as $functionName => $value) {
            call_user_func_array($functionName, $value);
        }
    }

    /**
     * Enable settings and load config files
     * @return null
     */
    public static function setup()
    {
        foreach (self::$settings as $functionName => $value) {
            call_user_func_array($functionName, $value);
        }
        self::loadConfigFile();
        self::defineConstants();
    }

    /**
     * Load config file and set Vars parameters
     * @return null
     */
    public static function loadConfigFile()
    {
        $configFile = ROOT. DS . "Config" . DS . "config.php";
        if (is_readable($configFile)) {
            require_once $configFile;
            self::$vars = $config;
        }
    }

    /**
     * Define constants for the app
     * @return null
     */
    public static function defineConstants()
    {
        foreach (self::$vars as $k => $v) {
            define($k, $v);
        }
    }
}
