<?php

namespace Supernova\Core;

use \Supernova\Core as Core;
use \Supernova\Core\Request as Request;
use \Supernova\Core\Elements as Elements;

class Elements extends \Supernova\Core
{
    /**
     * Initialize Supernova Elements
     */
    public static function setup()
    {
        require ROOT. DS . "Config" . DS . "routing.php";
        $currentAlias = "/".current(Core::$request['get']);
        if (array_key_exists($currentAlias, $routing['alias'])) {
            Core::$elements = $routing['alias'][$currentAlias];
        } elseif ($routing['actAsBehaviour']) {
            array_map('self::parseRequest', $routing['behaviourOrder']);
        }
    }

    /**
     * Parse every request
     * @param  string $eachCall Each request
     * @return null
     */
    private static function parseRequest($eachCall)
    {
        $class = "\Supernova\Core\Setter\\".ucfirst($eachCall);
        $$eachCall = $class::set(Request::getGetParameters());
        return ($$eachCall !== false) ? array_shift(Core::$request['get']) : null;
    }
}
