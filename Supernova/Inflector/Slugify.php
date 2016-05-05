<?php

namespace Supernova\Inflector;

class Slugify
{
    private static $regexReplaceInput = '~[^\\pL\d]+~u';
    private static $regexReplaceOutput = '~[^-\w]+~';
    private static $inputCharset = 'utf-8';
    private static $outputCharset = 'us-ascii//TRANSLIT';
    private static $defaultEmptyString = 'n-a';
    private static $defaultSeparator = '-';

    /**
     * Create slug from text
     * @param  string $text text
     * @return string       Returns slug
     */
    public static function word($text = '')
    {
        $text = preg_replace(self::$regexReplaceInput, self::$defaultSeparator, $text);
        $text = trim($text, self::$defaultSeparator);
        $text = iconv(self::$inputCharset, self::$outputCharset, $text);
        $text = strtolower($text);
        $text = preg_replace(self::$regexReplaceOutput, '', $text);
        return (empty($text)) ? $defaultEmptyString : $text;
    }
}
