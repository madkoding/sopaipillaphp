<?php

namespace Supernova\Blackhole;

use \Supernova\Blackhole\Generator as Generator;

class Keys extends \Supernova\Blackhole\Generator
{
    /**
     * Set primary model key in the parameters
     */
    public static function setPrimary()
    {
        foreach ((array)Generator::$params['fields'] as $field) {
            if ($field['Key'] == "PRI") {
                Generator::$params['primaryKey'] = $field['Field'];
            }
        }
    }

    /**
     * Set default model key in the parameters
     */
    public static function setDefault()
    {
        foreach ((array)Generator::$params['fields'] as $field) {
            if ($field['Key'] != "PRI" && $field['Key'] != "MUL") {
                preg_match("/(_id)/", $field['Field'], $matches);
                if (count($matches) == 0) {
                    Generator::$params['defaultKey'] = $field['Field'];
                    return;
                }
            }
        }
    }
}
