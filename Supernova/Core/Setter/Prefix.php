<?php

namespace Supernova\Core\Setter;

use \Supernova\Core as Core;
use \Supernova\Inflector\Camelize as Camelize;

class Prefix extends \Supernova\Core\Elements
{
    /**
     * Ingresa el nombre del prefijo
     * @param array $urlQuery Arreglo con request
     */
    public static function set($urlQuery)
    {
        require ROOT. DS . "Config" . DS . "routing.php";
        $prefix = ucfirst(Camelize::word(current($urlQuery)));
        if (in_array($prefix, $routing['prefix'])) {
            Core::$elements['prefix'] = $prefix;
            return true;
        }
        return false;
    }
}
