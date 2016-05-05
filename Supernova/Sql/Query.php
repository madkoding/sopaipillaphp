<?php

namespace Supernova\Sql;

use \Supernova\Security as Security;
use \Supernova\Inflector\Underscore as Underscore;
use \Supernova\Inflector\Singularize as Singularize;
use \Supernova\Inflector\Pluralize as Pluralize;

class Query extends \Supernova\Sql
{
    /**
     * Store SQL Query
     * @var array
     */
    protected $statement = array(
        "select" => "",
        "where" => array("1=1"),
        "orderBy" => array(),
        "groupBy" => array(),
        "limit" => "",
        "page" => 0
    );

    /**
     * Check parameters
     * @param  string $namespace Namespace name
     * @return null
     */
    public function check($namespace)
    {
        $this->parameters['namespace'] = $namespace;
        $tmp = explode("\\", $namespace);
        $this->parameters['model'] = end($tmp);
        $this->parameters['table'] = Underscore::word(Pluralize::word($this->parameters['model']));
        $this->parameters['columns'] = $this->getColumns();
    }
    
    public static function getList()
    {
        $namespace = get_called_class();
        $instance = new $namespace;
        $instance->check($namespace);
        $instance->statement["select"] = "SELECT ";
        $instance->find();
        $pk = "get".ucfirst($instance->primaryKey);
        $dk = "get".ucfirst($instance->defaultKey);
        $results = array();
        foreach ($instance as $item) {
            $results[$item->$pk()] = $item->$dk();
        }
        return $results;
    }
    
    /**
     * Initizalize select query
     * @param  array  $columns Array with columns
     * @return object          SQL model object
     */
    public static function select($columns = array())
    {
        $namespace = get_called_class();
        $instance = new $namespace;
        $instance->check($namespace);
        $instance->statement["select"] = "SELECT ";
        return $instance;
    }
    
    /**
     * Change select statement to distinct
     * @return object          SQL model object
     */
    public function distinct()
    {
        $instance->statement["select"] = "SELECT DISTINCT ";
        return $this;
    }
    
    /**
     * Set where args in statement
     * @param  string $args0 Column name
     * @param  string $args1 Operand
     * @param  string $args2 Condition
     * @return object          SQL model object
     */
    public function where($args0 = "1", $args1 = "=", $args2 = "1")
    {
        $this->statement["where"][] = "`".$args0."`".$args1."'".$args2."'";
        return $this;
    }
    
    /**
     * Set group by in query
     * @param  string $column column name to group
     * @return object          SQL model object
     */
    public function groupBy($column)
    {
        $this->statement["groupBy"][] = Security::sanitize($column);
        return $this;
    }
    
    /**
     * Set order by in query
     * @param  string $column Column name to order
     * @param  string $order  ASC or DESC
     * @return object          SQL model object
     */
    public function orderBy($column, $order = "ASC")
    {
        if ($order == "ASC" || $order == "DESC" || $order == "asc" || $order == "desc") {
            $this->statement["orderBy"][] = Security::sanitize($column)." ".$order;
        }
        return $this;
    }
    
    /**
     * Set limit for SQL Query
     * @param  string $limitNumber Limit number
     * @return object          SQL model object
     */
    public function limit($limitNumber = "1")
    {
        $this->statement["limit"] = $limitNumber;
        return $this;
    }
    
    /**
     * Set if results for query will be paged
     * @param  integer $pageSize Items per page
     * @return object          SQL model object
     */
    public function paged($pageSize = 10)
    {
        $pageStart = self::$page;
        $pageEnd = self::$page+$pageSize;
        $this->statement["limit"] = $pageStart.",".$pageSize;
        return $this;
    }
    
    /**
     * Build query
     * @return string Query string
     */
    public function buildQuery()
    {
        $query = $this->statement["select"].$this->parameters["columns"];
        $query.= " FROM `".$this->parameters["table"]."`";
        $query.= " WHERE ".implode(" AND ", $this->statement["where"]);
        $query.= (!empty($this->statement["groupBy"])) ? "GROUP BY ".implode(" AND ", $this->statement["groupBy"]) : "";
        $query.= (!empty($this->statement["orderBy"])) ? "ORDER BY ".implode(" AND ", $this->statement["orderBy"]) : "";
        $query.= (!empty($this->statement["limit"])) ? "LIMIT ".$this->statement["limit"] : "";
        $query.= ";";
        return $query;
    }
    
        
    /**
     * Get columns for table
     * @return string Columns
     */
    private function getColumns()
    {
        return "*";
    }
}
