<?php

namespace Supernova\Route;

use \Supernova\Route as Route;
use \Supernova\Inflector\Underscore as Underscore;

class Generate extends \Supernova\Route
{
    public static $routing;
    
    /**
     * Generate URL from array with params or string alias
     * @param  mixed $url String alias or array with params (prefix, controller, action, etc...)
     * @return string      Formated http URL
     */
    public static function url($url)
    {
        if (!is_array($url)) {
            if (array_key_exists($url, Generate::$routing['alias'])) {
                return Route::url(Generate::$routing['alias'][$url]);
            }
            return str_replace(' ', '-', $url);
        }
        return Route::getBaseUrl()."/".implode('/', self::newUrlArray($url));
    }

    /**
     * Generate new formated url array
     * @param  array $url   Url Array
     * @return array        Formated Url Array
     */
    private static function newUrlArray($url)
    {
        $newUrl = array();
        foreach (Generate::$routing['behaviourOrder'] as $eachOrder) {
            if (isset($url[$eachOrder])) {
                $newUrl[] = Underscore::word($url[$eachOrder]);
                unset($url[$eachOrder]);
            }
        }
        foreach ($url as $k => $v) {
            $newUrl[$k] = $v;
        }
        return array_filter($newUrl);
    }
}
