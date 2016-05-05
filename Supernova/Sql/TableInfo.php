<?php

namespace Supernova\Sql;

class TableInfo extends \Supernova\Sql
{
    /**
     * Get all tables from database
     * @return mixed Return tables arrays or false
     */
    public static function getTables()
    {
        $instancia = new self();
        if ($instancia->connect()) {
            $query = 'SHOW TABLES FROM '.$instancia->parameters["db_name"];
            $sth = $instancia->connection->prepare($query);
            return $sth->execute();
        }
        return false;
    }

    /**
     * Get column fields from table
     * @param  string $table Table name
     * @return array  $fields Table fields
     */
    public static function getFields($table = null)
    {
        if (empty($table)) {
            \Supernova\View::setError(500);
            \Supernova\View::callError(__("Table name missing").$table);
        }
        try {
            $instancia = new self();
            if ($instancia->connect()) {
                $query = 'SHOW FIELDS FROM '.$instancia->parameters["db_name"].".".$table;
                $sth = $instancia->connection->prepare($query);
                $sth->execute();
                return $sth->fetchAll();
            }
            return false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Check if table exists
     * @param  string $table  Table name
     * @param  string $column Column name
     * @return bool         Returns true or false
     */
    public static function checkField($table = null, $column = null)
    {
        if (empty($table)) {
            \Supernova\View::setError(500);
            \Supernova\View::callError(__("Table name missing").$table);
        }
        if (empty($column)) {
            \Supernova\View::setError(500);
            \Supernova\View::callError(__("Column name missing").$column);
        }
        $instancia = new self();
        if ($instancia->connect()) {
            $table = $instancia->parameters["db_name"].".".$table;
            $query = "SHOW COLUMNS FROM $table WHERE FIELD='$column'";
            $sth = $instancia->connection->prepare($query);
            $sth->execute();
            $row = $sth->fetch();
            return (empty($row)) ? false : true;
        }
        return false;
    }
}
