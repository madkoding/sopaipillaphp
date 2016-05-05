<?php

namespace Supernova;

class Table extends \Supernova\View
{
    /**
     * Model parameters
     * @var array
     */
    public static $model = array();

    /**
     * Fields from model
     * @var array
     */
    public static $fields = array();
    
    /**
     * Initialize table object
     * @param  string $objects Retrieve values from object
     * @return null
     */
    private static function initialize($objects)
    {
        if (!is_object($objects)) {
            trigger_error(
                __("Objects not found in value called for Helper::table. Array or string found"),
                E_USER_WARNING
            );
        }
        
        self::$model['className'] = get_class($objects);
        self::$model['classNameForm'] = self::$model['className']."Form";
        $modelName = explode("\\", self::$model['className']);
        self::$model['name'] = end($modelName);
        if (!is_null($objects->getFirst())) {
            self::$fields = array_keys($objects->getFirst()->toArray());
        } else {
            self::$fields = array();
        }
    }
    
    /**
     * Check every field from object and their labels
     * @return null
     */
    private static function checkFieldNames()
    {
        $className = self::$model['classNameForm'];
        if (empty($className)) {
            trigger_error(__('No object found'));
        }
        $form = new $className();
        foreach (self::$fields as $label => $field) {
            if (isset($form->settings[$label]["widget"]["label"])) {
                self::$fields[$label] = $form->settings[$label]["widget"]["label"];
            }
        }
    }
    
    /**
     * Retrieve values from object
     * @param  array  $args   Array with table options
     * @param  string $fields Fieldname
     * @param  string $values Values from field
     * @return string         Values
     */
    private static function getValues($args, $fields, $values)
    {
        if (isset($args['use']) && isset($args['use'][$fields])) {
            $object = $args['use'][$fields];
            
            if (is_array($object)) {
                if (isset($object[$values])) {
                    return htmlentities(utf8_decode($object[$values]));
                } else {
                    return "--";
                }
            }
             
            if (is_string($object)) {
                $functionName = explode("::", $object);
                if (method_exists($functionName[0], $functionName[1])) {
                    return $functionName[0]::$functionName[1]($values);
                }
            }
        }
        return htmlentities(utf8_decode($values));
    }
    
    /**
     * Retrieve actions for table items
     * @param  array  $args Options
     * @param  string $pk   Primary Key
     * @param  string $fk   Foreaing Key
     * @return string Action links
     */
    private static function getActions($args, $pk, $fk = "id")
    {
        if (isset($args['actions'])) {
            $actions = $args['actions'];
        } else {
            $actions = array('edit'=>__('Edit'), 'delete'=>__('Delete'));
        }
        
        $eachData = "";
        if ($actions) {
            $eachData.="<td>";
            $eachLink="";
            foreach ($actions as $action => $label) {
                $eachLink.= \Supernova\Helper::createLink(
                    array(
                        "href" => \Supernova\Route\Generate::url(
                            array(
                                "prefix" => \Supernova\Inflector\Underscore::word(
                                    \Supernova\Core::$elements['prefix']
                                ),
                                "controller" => \Supernova\Inflector\Underscore::word(
                                    \Supernova\Core::$elements['controller']
                                ),
                                "action" => $action,
                                $fk => $pk
                            )
                        ),
                        "text" => $label
                    )
                );
            }
            $eachData.=$eachLink;
            $eachData.="</td>";
        }
        return $eachData;
    }
    
    /**
     * Draw table
     * @param  array $args Parameters
     * @return string       Table HTML
     */
    public static function draw($args)
    {
        if (empty($args['values']) || is_null($args['values'])) {
            return "<table class='table table-striped' ><tr><td>".__("No results")."</td></tr></table>";
        }
        
        self::initialize($args['values']);
        
        self::checkFieldNames();
        
        $tableHead="";
        foreach (self::$fields as $label) {
            $tableHead.= "<th>".$label."</th>";
        }
        $tableHead.= "<th>".__('Actions')."</th>";

        $tableBody="";
        foreach ($args['values'] as $object) {
            $results = $object->toArray();
            $tableBody.="<tr>";
            foreach ($results as $fields => $values) {
                $tableBody.="<td>".self::getValues($args, $fields, $values)."</td>";
            }
            if (isset($results[$object->primaryKey])) {
                $tableBody.= self::getActions($args, $results[$object->primaryKey], $object->primaryKey);
            }
            $tableBody.="</tr>";
        }

        $output = <<<EOL
        <table class="table table-striped" >
            <thead>
                <tr>
                    $tableHead
                </tr>
            </thead>
            <tbody>
                $tableBody
            </tbody>
            <tfoot>
            </tfoot>
        </table>
EOL;

        return $output;
    }
}
