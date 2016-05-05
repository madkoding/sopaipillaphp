<?php

namespace Supernova;

/**
 * Cache del modelo de las tablas
 */
class Cache
{
    /**
     * Carga cache del modelo
     * @param  string $model Nombre del modelo
     * @return string        Esquema del modelo desencriptado
     */
    public static function load($model)
    {
        $filename = ROOT.DS."Cache".DS.$model;
        if (!file_exists($filename)) {
            self::generate($model);
        }
        parse_str(file_get_contents($filename), $output);
        return $output;
    }

    /**
     * Genera cache del modelo
     * @param  string $model Nombre del modelo
     * @return null
     */
    public static function generate($model)
    {
        $dirName = ROOT.DS.'Cache';
        $filename = $dirName.DS.$model;
        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }
        if (is_writable($dirName)) {
            $table = \Supernova\Inflector\Underscore::word(\Supernova\Inflector\Pluralize::word($model));
            $fields = \Supernova\Sql\TableInfo::getFields($table);
            file_put_contents($filename, http_build_query($fields));
        } else {
            trigger_error(__('Cache folder is not writable, check permissions', E_USER_ERROR));
        }
    }
}
