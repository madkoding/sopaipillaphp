<?php

namespace Supernova;

use \Supernova\Core as Core;
use \Supernova\Route\Generate as Generate;

class Route
{
    /**
     * Route parameters
     * @var array
     */
    public static $params = array(
        'protocol' => 'http:/',
        'hostName' => 'localhost',
        'relativePath' => '',
        'publicFolder' => 'Public'
    );

    /**
     * Initialize routes
     * @return null
     */
    public static function initialize()
    {
        self::$params['protocol'] = (Core::checkSSL()) ? "https:/" : "http:/";
        self::$params['hostName'] = str_replace("/", "", $_SERVER['HTTP_HOST']);
        self::$params['relativePath'] = self::findRelativePath();
        self::$params['publicFolder'] = self::findPublicFolder();
        require_once ROOT. DS . "Config" . DS . "routing.php";
        Generate::$routing = $routing;
    }

    /**
     * Find public folder
     * @return string Public folder name
     */
    public static function findPublicFolder()
    {
        if (HTACCESS) {
            $public_directory = dirname($_SERVER['PHP_SELF']);
            $directory_array = explode('/', $public_directory);
            return end($directory_array);
        }
        return self::$params['publicFolder'];
    }

    /**
     * Find relative Path
     * @return string Relative path
     */
    public static function findRelativePath()
    {
        $path = explode('/', dirname($_SERVER['PHP_SELF']));
        unset($path[array_search(self::$params['publicFolder'], $path)]);
        $path = array_filter($path);
        return ($path) ? implode("/", $path) : "";
    }

    /**
     * Get Public URL
     * @return string Formated http public URL
     */
    public static function getPublicUrl()
    {
        $url[] = self::getBaseUrl();
        $url[] = (self::$params['publicFolder']) ? self::$params['publicFolder'] : self::findRelativePath();
        array_filter($url);
        return implode('/', $url);
    }

    /**
     * Get Base URL
     * @return string Formated http base URL
     */
    public static function getBaseUrl()
    {
        $url[] = self::$params['protocol'];
        $url[] = self::$params['hostName'];
        $url[] = self::$params['relativePath'];
        return implode('/', $url);
    }
}
