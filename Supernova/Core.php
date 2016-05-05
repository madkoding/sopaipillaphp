<?php

namespace Supernova;

use \Supernova\Security as Security;
use \Supernova\Session as Session;
use \Supernova\Translate as Translate;
use \Supernova\Inflector as Inflector;
use \Supernova\Core\Request as Request;

class Core
{
    /**
     * Store Namespace
     * @var string
     */
    public static $namespace;

    /**
     * Store elements
     * @var array
     */
    public static $elements = array(
        "prefix" => "",
        "controller" => "",
        "action" => "Index"
    );

    /**
     * Store requests
     * @var array
     */
    public static $request = array();
    
    /**
     * Initialize entire core
     * @return null
     */
    public static function initialize()
    {
        date_default_timezone_set(TIMEZONE);
        $_SERVER['CONTENT_TYPE'] = "application/x-www-form-urlencoded";
        session_start();
        Security::cleanAll();
        Translate::setLanguage(LANGUAGE_DEFAULT);
        Request::setPostParameters(Security::sanitize($_POST));
        Request::setFilesParameters(Security::sanitize($_FILES));
    }

    /**
     * Check for SSL connection
     * @return boolean Returns true or false
     */
    public static function checkSSL()
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? true : false;
    }
}
