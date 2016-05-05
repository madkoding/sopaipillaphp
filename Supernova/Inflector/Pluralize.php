<?php

namespace Supernova\Inflector;

class Pluralize
{
    /**
     * Regex origin by language
     * @var array
     */
    private static $origin = array(
        'es' => array(
            '/([rtbaeiou])([A-Z]|_|$)/',
            '/([rlnd])([A-Z]|_|$)/',
            '/(is)([A-Z]|_|$)/',
            '/(i)(z)([A-Z]|_|$)/'
        ),
        'en' => array(
            '/([rtbaeioun])([A-Z]|_|$)/',
            '/([rld])([A-Z]|_|$)/',
            '/(is)([A-Z]|_|$)/',
            '/(i)(z)([A-Z]|_|$)/'
        )
    );

    /**
     * Regex destination
     * @var array
     */
    private static $destiny = array(
        '\1s\2',
        '\1es\2',
        '\1es',
        '\1ces'
    );

    /**
     * Pluralize word
     * @param  string $str Word in singular
     * @return string      Word in plural
     */
    public static function word($str)
    {
        $language = (defined(LANGUAGE_BASE) && in_array(LANGUAGE_BASE, self::$origin)) ? LANGUAGE_BASE : 'en';
        return preg_replace(self::$origin[$language], self::$destiny, $str);
    }
}
