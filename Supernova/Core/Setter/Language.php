<?php

namespace Supernova\Core\Setter;

use \Supernova\Translate as Translate;

class Language extends \Supernova\Core\Elements
{
    /**
     * Ingresa el prefijo de lenguaje
     * @param array $urlQuery Arreglo con request
     */
    public static function set($urlQuery)
    {
        $language = current($urlQuery);
        $file = ROOT.DS.'Locale'.DS.$language.'.php';
        if (is_readable($file) || $language == "en") {
            Translate::setLanguage($language);
            return true;
        }
        Translate::setLanguage(LANGUAGE_DEFAULT);
        return false;
    }
}
