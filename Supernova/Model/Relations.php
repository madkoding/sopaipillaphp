<?php

namespace Supernova\Model;

trait Relations
{
    /**
     * Find relations from the model and return the values into the results
     * @param  string $columnName Camelized column name
     * @return array  Array with values
     */
    private function findRelation($columnName)
    {
        $singular = \Supernova\Inflector\Singularize::word($columnName);
        $plural = \Supernova\Inflector\Pluralize::word($columnName);
        $namespace = "\App\Model\\".$singular;
        $vars = get_class_vars($namespace);
        $foreingKey = \Supernova\Inflector\Underscore::word($singular)."_".$vars['primaryKey'];
        $relations = array('hasMany', 'belongsTo', 'hasAndBelongsTo');
        foreach ($relations as $relation) {
            if (isset($this->$relation) && !empty($this->$relation)) {
                if (array_search($singular, $this->$relation) !== false) {
                    if ($columnName == $plural) {
                        return $namespace::select()
                            ->where($vars['primaryKey'], "=", $this->results[$foreingKey])
                            ->find();
                    } elseif ($columnName == $singular) {
                        return current(
                            $namespace::select()
                            ->where($vars['primaryKey'], "=", $this->results[$foreingKey])
                            ->find()
                        );
                    }
                }
            }
        }
        return false;
    }
}
