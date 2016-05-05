<?php

namespace Supernova;

use \Supernova\Core as Core;
use \Supernova\Inflector\Singularize as Singularize;
use \Supernova\Inflector\Pluralize as Pluralize;
use \Supernova\Blackhole\Create as Generator;

class Autoload
{
    /**
     * Set PHP extension for class
     * @var string
     */
    private $ext = "php";

    /**
     * Get filename from class
     * @param  string $className Camelized class name
     * @return string            Filename
     */
    private function getFilename($className)
    {
        return ROOT.DS.str_replace('\\', DS, $className);
    }
    
    /**
     * Load class into memory
     * @param  string $filename Class filename
     * @return null
     */
    private function loadClass($filename)
    {
        if (is_readable($filename)) {
            require_once $filename;
        }
    }
    
    /**
     * Autoloader function registered in memory
     * @param  string $className Camelized class name
     * @return null
     */
    public function autoLoadPhp($className)
    {
        $file = $this->getFilename($className);
        $this->loadClass($file.".".$this->ext);
        $this->loadClass($file.".class.".$this->ext);
        $this->loadClass($this->autoGenerateModel($file.".".$this->ext));
        $this->loadClass($this->autoGenerateController($file.".".$this->ext));
    }
    
    /**
     * Auto generate model file
     * @param  string $file Filename
     * @return string       Filename
     */
    public function autoGenerateModel($file)
    {
        if (strpos($file, "App".DS."Model") !== false) {
            $modelName = Singularize::word(Core::$elements['controller']);
            $Generator = new Generator($modelName);
            if (method_exists($Generator, "model")) {
                $Generator->model();
                $Generator->modelForm();
            }
        }
        return $file;
    }

    /**
     * Auto generate controller file
     * @param  string $file Filename
     * @return string       Filename
     */
    public function autoGenerateController($file)
    {
        if (strpos($file, "App".DS."Controller") !== false) {
            $modelName = Pluralize::word(Core::$elements['controller']);
            $Generator = new Generator($modelName);
            $Generator->controller();
        }
        return $file;
    }

    /**
     * Register autoloader in memory
     * @return null
     */
    public function register()
    {
        spl_autoload_register(array($this, "autoLoadPhp"));
    }
}
