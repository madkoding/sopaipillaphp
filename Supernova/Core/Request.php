<?php

namespace Supernova\Core;

use \Supernova\Core as Core;
use \Supernova\Security as Security;

class Request extends \Supernova\Core
{
    /**
     * Ingresa parametros GET
     * @param array $params Parametros GET
     */
    public static function setGetParameters($params = array())
    {
        Core::$request['get'] = Security::sanitize($params);
        unset($_GET);
    }

    /**
     * Ingresa parametros POST
     * @param array $params Parametros POST
     */
    public static function setPostParameters($params = array())
    {
        Core::$request['post'] = Security::sanitize($params);
        unset($_POST);
    }

    /**
     * Ingresa parametros de FILES (Archivos)
     * @param array $params Parametros FILES
     */
    public static function setFilesParameters($params = array())
    {
        Core::$request['files'] = Security::sanitize($params);
        unset($_FILES);
    }

    /**
     * Obtiene parametros de FILES (Archivos)
     * @return array Parametros de FILES
     */
    public static function getFilesParameters()
    {
        return Core::$request['files'];
    }

    /**
     * Obtiene parametros GET
     * @return array Parametros GET
     */
    public static function getGetParameters()
    {
        return Core::$request['get'];
    }

    /**
     * Obtener parametros POST
     * @return array Parametros POST
     */
    public static function getPostParameters()
    {
        return Core::$request['post'];
    }

    /**
     * Ingresa el request de la ruta
     */
    public static function setup()
    {
        self::setGetParameters(explode('/', $_SERVER['QUERY_STRING']));
    }
}
