<?php

namespace Supernova;

use \Supernova\Inflector\Singularize as Singularize;
use \Supernova\Inflector\Pluralize as Pluralize;
use \Supernova\Inflector\Camelize as Camelize;
use \Supernova\Inflector\Underscore as Underscore;
use \Supernova\Route\Generate as Generate;
use \Supernova\View as View;

class Controller
{
    /**
     * Model names
     * @var array
     */
    public static $model = array();

    /**
     * Action name
     * @var [type]
     */
    public static $action;

    /**
     * Initialize controller
     */
    public function __construct()
    {
        self::$model['singular'] = ucfirst(Singularize::word(\Supernova\Core::$elements['controller']));
        self::$model['plural'] = ucfirst(Pluralize::word(\Supernova\Core::$elements['controller']));
        self::$action = ucfirst(Camelize::word(\Supernova\Core::$elements['prefix']));
        self::$action.= ucfirst(Camelize::word(\Supernova\Core::$elements['action']));
    }

    /**
    * Set variables to the view
    * @param    mixed   $name   Value name
    * @param    mixed   $value  Value
    */
    public static function set($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                self::set($key, $value);
            }
        } else {
            View::set($name, $value);
        }
    }

    /**
     * Redirect to an URL
     * @param   mixed   $url   Url or array with arguments
     */
    public static function redirect($url = null)
    {
        ob_start();
        header('Location:'.Generate::url($url));
        ob_flush();
        die();
    }

    /**
     * Set flash messages for the view
     * @param  array $args  array("Message","type")
     * @return null
     */
    public static function flash($args)
    {
        $class = (isset($args['status'])) ? $args['status'] : "success";
        $output= "<div class='alert alert-$class alert-dismissable'>";
        $output.= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $output.= (isset($args['message'])) ? $args['message'] : '' ;
        $output.= '</div>';
        View::setMessage($output);
    }

    /**
     * Defines layout used for template
     * @param string $layout Layout name
     */
    public static function setLayout($layout = "default")
    {
        View::$layout = $layout;
    }

    /**
     * Disable an action from controller
     * @return void
     */
    public static function disabled()
    {
        debug(__("Disabled view"));
        View::setError(404);
    }
}
