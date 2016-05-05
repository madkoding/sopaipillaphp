<?php

namespace Supernova\Inflector;

class Underscore
{
    private static $regexRule = '/([A-Z])/';
    
    /**
     * Camelize to Underscore string
     * @param  string $str Camelized string
     * @return string      Underscore string
     */
    public static function word($str = '')
    {
        if (is_string($str) && !empty($str)) {
            $str[0] = strtolower($str[0]);
            $func = create_function('$c', 'return "_" . strtolower($c[1]);');
            return preg_replace_callback(self::$regexRule, $func, (string) $str);
        }
        return $str;
    }
}
