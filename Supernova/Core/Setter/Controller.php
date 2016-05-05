<?php

namespace Supernova\Core\Setter;

use \Supernova\Core as Core;
use \Supernova\Inflector\Camelize as Camelize;
use \Supernova\View as View;

class Controller extends \Supernova\Core\Elements
{
    /**
     * Ingresa el nombre del controlador
     * @param array $urlQuery Arreglo con request
     */
    public static function set($urlQuery)
    {
        if ($controller = Camelize::word(current($urlQuery))) {
            Core::$elements['controller'] = $controller;
            return true;
        }
        View::setError(404);
        trigger_error(__("No controller called"));
        return false;
    }
}
