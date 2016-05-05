<?php

namespace Supernova;

class View extends \Supernova\Controller
{
    /**
     * Values for template
     * @var array
     */
    public static $values = array();

    /**
     * Default Template name
     * @var string
     */
    public static $prefix = "Default";

    /**
     * Default Layout name
     * @var string
     */
    public static $layout = "default";

    /**
     * Stores model name
     * @var string
     */
    public static $model;

    /**
     * Stores namespace
     * @var string
     */
    public static $namespace;

    /**
     * Stores view name
     * @var string
     */
    public static $viewName;

    /**
     * Stores view layout files
     * @var array
     */
    public static $viewFiles = array();

    /**
     * Stores HTTP error number to show
     * @var integer
     */
    public static $errorNumber = 0;

    /**
     * Set values for the template
     * @param string $name  Field name
     * @param mixed $value  Values
     */
    public static function set($name, $value = null)
    {
        self::$values[$name] = $value;
    }

    /**
     * Set session message
     * @param string $msg Message
     * @param string $key Key name
     */
    public static function setMessage($msg, $key = 'message')
    {
        \Supernova\Session::create($key, $msg);
    }

    /**
     * Get session message
     * @param   String  $key    Key name
     */
    public static function getMessage($key = 'message')
    {
        //if (empty($this->errors)) {
        $msg = \Supernova\Session::read($key);
        if (!empty($msg)) {
            \Supernova\Session::destroy($key);
            return $msg;
        }
    }

    /**
     * Generate view file if does not exist
     * @param  string $model Model name
     * @return null
     */
    private static function generateIfNotExist($model)
    {
        preg_match("/(index|add|edit|delete)/i", \Supernova\Core::$elements['action'], $matches);
        $actionName = ucFirst(current($matches));
        $functionName = "view".$actionName;
        $Generator = new \Supernova\Blackhole\Create($model);
        if (method_exists($Generator, $functionName)) {
            $Generator->$functionName();
        }
    }

    /**
     * Initialize view parameters
     * @return null
     */
    private static function initialize()
    {
        self::$model = \Supernova\Inflector\Singularize::word(\Supernova\Core::$elements['controller']);
        if (!empty(\Supernova\Core::$elements['prefix'])) {
            self::$prefix = \Supernova\Core::$elements['prefix'];
        }
        self::$namespace = "\App\Model\\".self::$model;
        self::$viewName = \Supernova\Inflector\Underscore::word(
            \Supernova\Core::$elements['prefix'].
            \Supernova\Core::$elements['action']
        );
    }

    /**
     * Load view in memory
     * @return null
     */
    private static function loadView()
    {
        $viewFile = "View".DS.self::$model.DS.self::$viewName.".php";
        self::$viewFiles[] = ROOT.DS."App".DS.$viewFile;
        self::$viewFiles[] = ROOT.DS."Plugins".DS.self::$model.DS.$viewFile;
        self::generateIfNotExist(self::$model);
    }

    /**
     * Show error if view file not found
     * @return null
     */
    private static function viewNotFound()
    {
        $actionFile = \Supernova\Core::$elements['prefix'].\Supernova\Core::$elements['action'];
        $actionFile = \Supernova\Inflector\Underscore::word($actionFile).".php";
        $controllerMsg = \Supernova\Core::$elements['controller'];
        trigger_error(__("View not found:")." ".$actionFile." ".__('in')." /App/View/".$controllerMsg);
        \Supernova\View::setError(404);
    }

    /**
     * Show template and layout
     */
    public static function render()
    {
        self::initialize();
        self::loadView();
        
        $content_for_layout = self::getContent(self::$viewFiles);
        if ($content_for_layout === false) {
            self::viewNotFound();
            return;
        }

        $layoutFile = ROOT.DS."Public".DS.self::$prefix.DS.self::$layout.".php";
        if (is_readable($layoutFile)) {
            include($layoutFile);
        } else {
            trigger_error(__("Layout")." ".self::$layout.".php ".__("not found in prefix")." ".$prefix);
        }
    }

    /**
     * Get template content
     * @param  array  $views Array with template names
     * @return mixed         Return the first content found or false
     */
    public static function getContent($views = array())
    {
        extract(self::$values);
        ob_start((ini_get("zlib.output_compression") == 'On') ? "ob_gzhandler" : null);
        foreach ($views as $view) {
            if (file_exists($view)) {
                include($view);
                $content = ob_get_contents();
                ob_end_clean();
                return $content;
            }
        }
        return false;
    }

    /**
     * Include CSS into the layout
     * @param  string $cssFile CSS file without extension
     * @return string          HTML Link to CSS
     */
    public static function includeCss($cssFile)
    {
        $publicUrl = \Supernova\Route::getPublicUrl();
        $prefix = (empty(\Supernova\Core::$elements['prefix'])) ? "Default" : \Supernova\Core::$elements['prefix'];
        $output = "<link rel='stylesheet' href='$publicUrl/$prefix/css/$cssFile.css' />";
        return $output;
    }

    /**
     * Include JS into the layout
     * @param  string $jsFile JS file without extension
     * @return string         HTML link to JS
     */
    public static function includeJs($jsFile)
    {
        $publicUrl = \Supernova\Route::getPublicUrl();
        $prefix = (empty(\Supernova\Core::$elements['prefix'])) ? "Default" : \Supernova\Core::$elements['prefix'];
        $output = "<script type='text/javascript' src='$publicUrl/$prefix/js/$jsFile.js' ></script>";
        return $output;
    }

    /**
     * Set HTTP error number
     * @param integer $num HTTP Number error
     */
    public static function setError($num = 404)
    {
        self::$errorNumber = $num;
    }

    /**
     * Call to the error page
     * @param  string  $encodedError Encripted error string
     */
    public static function callError($encodedError = "")
    {
        include(ROOT.DS.\Supernova\Route::$params['publicFolder'].DS."errors".DS.self::$errorNumber.'.php');
        die();
    }
}
