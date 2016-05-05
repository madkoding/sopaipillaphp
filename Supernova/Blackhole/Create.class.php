<?php

namespace Supernova\Blackhole;

use \Supernova\Blackhole\Generator as Generator;
use \Supernova\Blackhole\Files as Files;

class Create extends \Supernova\Blackhole\Generator
{
    /**
     * Create controller file
     * @return object Return generator object
     */
    public function controller()
    {
        Files::checkWritablePath(self::$params["path"]."Controller");
        $controllerFileName = self::$params["path"]."Controller".DS.self::$params["modelName"]."Controller.php";
        Files::createIfNotExist($controllerFileName, "Controller");
        return $this;
    }
    
    /**
     * Create model file
     * @return object Return generator object
     */
    public function model()
    {
        if (self::$params["fields"]) {
            Files::checkWritablePath(self::$params["path"]."Model");
            Files::createIfNotExist(self::$params["path"]."Model".DS.self::$params["modelName"].".php", "Model");
            return $this;
        } else {
            return false;
        }
    }
    
    /**
     * Create model form file
     * @return object Return generator object
     */
    public function modelForm()
    {
        if (self::$params["fields"]) {
            Files::checkWritablePath(self::$params["path"]."Model");
            Files::createIfNotExist(self::$params["path"]."Model".DS.self::$params["modelName"]."Form.php", "ModelForm");
            return $this;
        } else {
            return false;
        }
    }
    
    /**
     * Create view - index
     * @return object Return generator object
     */
    public function viewIndex()
    {
        Files::checkWritablePath(self::$params["path"]."View".DS.self::$params["modelName"]);
        Files::createIfNotExist(self::$params["path"]."View".DS.self::$params["modelName"].DS."index.php", "ViewIndex");
        return $this;
    }
    
    /**
     * Create view - edit
     * @return object Return generator object
     */
    public function viewEdit()
    {
        Files::checkWritablePath(self::$params["path"]."View".DS.self::$params["modelName"]);
        Files::createIfNotExist(self::$params["path"]."View".DS.self::$params["modelName"].DS."edit.php", "ViewEdit");
        return $this;
    }
    
    /**
     * Create view - add
     * @return object Return generator object
     */
    public function viewAdd()
    {
        Files::checkWritablePath(self::$params["path"]."View".DS.self::$params["modelName"]);
        Files::createIfNotExist(self::$params["path"]."View".DS.self::$params["modelName"].DS."add.php", "ViewAdd");
        return $this;
    }
}
