<?php

namespace Supernova\Core;

use \Supernova\Core as Core;

class Action extends \Supernova\Core\Check
{
    /**
     * Load action in memory
     * @return boolean Returns true or false
     */
    public static function load()
    {
        $actionNames = array(
            "execute".Core::$elements['prefix'].Core::$elements['action'],
            "execute".Core::$elements['action']
        );
        foreach ($actionNames as $action) {
            if (self::execute($action)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Execute action from memory
     * @param  string $actionName Action Name
     * @return boolean             Returns true or false
     */
    public static function execute($actionName)
    {
        $namespace = Core::$namespace;
        if (method_exists($namespace, $actionName)) {
            $controllerClass = new $namespace();
            call_user_func_array(array($controllerClass, $actionName), Core::$request['get']);
            return true;
        }
        return false;
    }
}
