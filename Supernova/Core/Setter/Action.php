<?php

namespace Supernova\Core\Setter;

use \Supernova\Core as Core;
use \Supernova\Inflector\Camelize as Camelize;
use \Supernova\View as View;

class Action extends \Supernova\Core\Elements
{
    /**
     * Ingresa el nombre de la acción
     * @param array $urlQuery Arreglo con request
     */
    public static function set($urlQuery)
    {
        if ($action = Camelize::word(current($urlQuery))) {
            Core::$elements['action'] = $action;
        }
        return true;
    }
}
