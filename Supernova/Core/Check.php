<?php

namespace Supernova\Core;

use \Supernova\Core as Core;
use \Supernova\Inflector as Inflector;

/**
 * Check every part needed for the core to work
 */
class Check extends \Supernova\Core
{
    /**
     * Controller class namespaces
     * @var array
     */
    private static $controllerClases = array(
        "\App\Controller\\%controllerName%Controller",
        "\Vendor\\%controllerName%Controller\\%controllerName%Controller"
    );

    /**
     * Dependendes that should be loaded in memory
     * @var array
     */
    private static $dependences = array('mcrypt','mysql','pdo');

    /**
     * Check necesary dependences from PHP
     * @return boolean Returns true or false
     */
    public static function modules()
    {
        foreach (self::$dependences as $dependence) {
            $extensions[] = self::moduleLoaded($dependence);
        }
        return (in_array(0, $extensions)) ? true : false;
    }

    /**
     * Check if PHP Module is loaded in memory
     * @param  array  $extension Array with module names
     * @return boolean      Returns true or false
     */
    private static function moduleLoaded($extension)
    {
        if (!extension_loaded($extension)) {
            \Supernova\View::setError(500);
            trigger_error(__("Extension")." ".$extension." ".__("not loaded"), E_USER_ERROR);
            return 0;
        }
        return 1;
    }

    /**
     * Check controller and load in memory
     * @return boolean Returns true or false
     */
    public static function controller()
    {
        foreach (self::$controllerClases as $controller) {
            if (self::controllerExists($controller)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if controller exists
     * @param  string $controller Controller name
     * @return boolean            Returns true or false
     */
    private static function controllerExists($controller)
    {
        $controller = inject($controller, array("controllerName" => Core::$elements['controller']));
        if (class_exists($controller)) {
            Core::$namespace = $controller;
            return 1;
        }
        \Supernova\View::setError(404);
        trigger_error(__("Controller not exist:")." <strong>".$controller."</strong>");
        return 0;
    }

    /**
     * Check if action exists and execute
     * @return boolean Returns true or false with errors
     */
    public static function action()
    {
        if (\Supernova\Core\Action::load()) {
            return true;
        }
        $actionMsg = __("Action not exist:")." <strong>execute".Core::$elements['action']."</strong> ";
        $controllerMsg = __("in controller:")." <strong>".Core::$elements['controller']."</strong>";
        \Supernova\View::setError(404);
        trigger_error($actionMsg.$controllerMsg);
        return false;
    }
}
