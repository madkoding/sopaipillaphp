<?php

namespace Supernova;

use \Supernova\Inflector as Inflector;
use \Supernova\Debug as Debug;
use \Supernova\Security as Security;

class Sql
{
    /**
     * Static instance for SQL Object
     * @var null
     */
    protected static $instance = null;

    /**
     * Store connection
     * @var null
     */
    protected $connection = null;

    /**
     * Store PDO Error messages
     * @var array
     */
    private static $pdoErrorMsgs = array(
        '0' => 'No conection parameters',
        '2002' => 'Incorrect Host',
        '1044' => 'Incorrect Username',
        '1045' => 'Incorrect Password',
        '1049' => 'Incorrect Database Name',
        '42S02' => 'Table not found in database',
        '42S22' => 'Column not found in Table',
        '42000' => 'Syntax error or Access violation'
    );
    
    /**
     * Store SQL Parameters
     * @var array
     */
    protected $parameters = array(
        "db_name" => "",
        "namespace" => "",
        "model" => "",
        "table" => "",
        "columns" => ""
    );
    
    public function check($namespace)
    {
        $this->parameters['namespace'] = $namespace;
        $tmp = explode("\\", $namespace);
        $this->parameters['model'] = end($tmp);
        $this->parameters['table'] = \Supernova\Inflector\Underscore::word(
            \Supernova\Inflector\Pluralize::word(
                $this->parameters['model']
            )
        );
        $this->parameters['columns'] = \Supernova\Sql\TableInfo::getFields($this->parameters['table']);
    }

    /**
     * Find the query and return results
     * @return object Results object
     */
    public function find()
    {
        $query = $this->buildQuery();
        Debug::logQuery($query);
        try {
            if ($this->connect()) {
                $this->connection->beginTransaction();
                $result = $this->connection->prepare($query);
                $result->setFetchMode(\PDO::FETCH_CLASS, $this->parameters['namespace']);
                $result->execute();
                $this->connection->commit();
                while ($row = $result->fetch()) {
                    $this->results[] = $row;
                }
                return $this;
            }
        } catch (\PDOException $e) {
            $this->connection->rollBack();
            $this->pdoErrors($e);
        }
        return false;
    }
    
    /**
     * Return the first result from query
     * @return object First object result
     */
    public function findOne()
    {
        $this->limit = "1";
        return $this->find()->getFirst();
    }
    
    public static function findOneByPk($id = null)
    {
        return $this->where("id", "=", $id)->findOne();
    }

    /**
     * Initialize SQL Connection
     * @return boolean Returns true or false
     */
    public function connect()
    {
        try {
            require ROOT. DS . "Config" . DS . "database.php";
            extract($dbconfig[ENVIRONMENT]);
            $dbn = $driver.":host=".$host.";dbname=".$database;
            $this->parameters["db_name"] = $database;
            $this->connection = new \PDO($dbn, $username, $password);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $this->connection->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_NATURAL);
            $this->connection->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES UTF8");
            return true;
        } catch (\PDOException $e) {
            $this->pdoErrors($e);
        }
        return false;
    }

    /**
     * Show PDO Errors
     * @param  object $e Error object
     * @return string    Error message
     */
    public function pdoErrors($e)
    {
        $conFailed = 'Conection failed';
        if (isset(self::$pdoErrorMsgs[$e->getCode()])) {
            $errorMsg = self::$pdoErrorMsgs[$e->getCode()];
        } else {
            $errorMsg = $e->getMessage();
        }
        $extra = (isset($e->errorInfo[2])) ? explode("'", $e->errorInfo[2]) : "";
        $extra2 = (isset($extra[2])) ? ' -> '.$extra[2] : "";
        $msg = __($conFailed).' :: '.__($errorMsg).$extra2;
        trigger_error($msg);
    }
}
