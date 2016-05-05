<?php

namespace Supernova\Inflector;

class Camelize
{
    private static $regexRule = '/_([a-z])/';
    /**
    * Underscore to camelize string
    * @param  string  $str             Underscore string
    * @param  boolean $capitaliseFirst Capitalize first character
    * @return string                   Camelized string
    */
    public static function word($str = '', $capitaliseFirst = true)
    {
        if (is_string($str) && !empty($str)) {
            if ($capitaliseFirst) {
                $str[0] = strtoupper($str[0]);
            }
            $func = create_function('$c', 'return strtoupper($c[1]);');
            return preg_replace_callback(self::$regexRule, $func, $str);
        }
        return $str;
    }
}
