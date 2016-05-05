<?php

namespace Supernova\Inflector;

class Singularize
{
    /**
     * Regex origin by language
     * @var array
     */
    private static $origin = array(
        'es' => array(
            '/([rln])es([A-Z]|_|$)/',
            '/ises([A-Z]|_|$)/',
            '/ices([A-Z]|_|$)/',
            '/([d])es([A-Z]|_|$)/',
            '/([rbtaeiou])s([A-Z]|_|$)/'
        ),
        'en' => array(
            '/([ln])es([A-Z]|_|$)/',
            '/ises([A-Z]|_|$)/',
            '/ices([A-Z]|_|$)/',
            '/([d])es([A-Z]|_|$)/',
            '/([rbtaeioun])s([A-Z]|_|$)/'
        )
    );

    /**
     * Regex destination
     * @var array
     */
    private static $destiny = array(
        '\1\2',
        '\1is',
        '\1iz',
        '\1',
        '\1\2'
    );

    /**
     * Singularize word
     * @param  string $str Word in plural
     * @return string      Word in singular
     */
    public static function word($str)
    {
        $language = (defined(LANGUAGE_BASE) && in_array(LANGUAGE_BASE, self::$origin)) ? LANGUAGE_BASE : 'en';
        return preg_replace(self::$origin[$language], self::$destiny, $str);
    }
}
