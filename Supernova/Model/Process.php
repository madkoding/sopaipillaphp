<?php

namespace Supernova\Model;

trait Process
{
    /**
     * Proccess post and save to database
     * @return mixed Returns true or execute the onError function from the model;
     */
    public static function post()
    {
        if ($post = \Supernova\Core\Request::getPostParameters()) {
            $model = \Supernova\Inflector\Singularize::word(\Supernova\Core::$elements['controller']);
            $namespace = "\App\Model\\".$model;
            $formNamespace = $namespace."Form";
            $objectForm = new $formNamespace();
            foreach ($post['data'] as $model => $values) {
                if ($namespace == "\App\Model\\".ucfirst(\Supernova\Inflector\Camelize::word($model))) {
                    $object = new $namespace();
                    $object = self::setValues($values, $objectForm, $object);
                    return ($object->isValid()) ? true : $object->onError();
                }
            }
        }
        return null;
    }

    /**
     * Set values for post
     * @param mixed $values     Values
     * @param obj $objectForm Object form
     * @param obj $object     Results object
     */
    private static function setValues($values, $objectForm, $object)
    {
        foreach ($values as $key => $val) {
            $setter = "set".ucfirst(\Supernova\Inflector\Camelize::word($key));
            if (isset($objectForm->schema[$key]['widget']['use'])) {
                if ($uses = $objectForm->schema[$key]['widget']['use']) {
                    $functionName = explode("::", $uses);
                    if (method_exists($functionName[0], $functionName[1])) {
                        $val = $functionName[0]::$functionName[1]($val);
                    }
                }
            }
            $object->$setter($val);
        }
        return $object;
    }

    /**
     * Proccess files from post
     * @return boolean  Return true or false if files found
     */
    public static function files()
    {
        $files = \Supernova\Core\Request::getFilesParameters();
        if (!empty($files)) {
            //debug($files);
            return true;
        }
        return false;
    }

    /**
     * Check if form model and process posts and files
     * @return boolean Returns true or false if class
     */
    public static function form()
    {
        $namespace = "\App\Model\\".\Supernova\Inflector\Singularize::word(\Supernova\Core::$elements['controller']);
        if (class_exists($namespace)) {
            $namespace::post();
            $namespace::files();
            return true;
        }
        return false;
    }
}
