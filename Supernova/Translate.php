<?php

namespace Supernova;

class Translate
{
    private static $language;
    private static $lang = array();

    /**
     * Adjust default language to show
     * @param string $language Language prefix
     */
    public static function setLanguage($language = "en")
    {
        self::$language = (!empty($language)) ? $language : LANGUAGE_DEFAULT;
    }

    /**
     * Translate text
     * @param  string $str Text
     * @return string      Translated text
     */
    public static function text($str)
    {
        $file = ROOT.DS.'Locale'.DS.self::$language.'.php';
        if (file_exists($file)) {
            include $file;
            if (array_key_exists($str, $language)) {
                $str = htmlentities($language[$str]);
            }
        }
        return $str;
    }
}
