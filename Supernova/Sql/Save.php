<?php

namespace Supernova\Sql;

use \Supernova\Debug as Debug;

class Save extends \Supernova\Sql
{
    /**
     * Insert or update query into database
     * @param  array $results   Results array
     * @param  object $object    Results object
     * @param  object $instancia Model object instance
     * @return boolean            Returns true or false
     */
    private static function query($results, $object, $instancia)
    {
        if (isset($results[$object->primaryKey])) {
            if (is_null($results[$object->primaryKey]) || empty($results[$object->primaryKey])) {
                $results[$object->primaryKey] = 'dummy';
                foreach ($results as $k => $v) {
                    $keysName[$k] = $v;
                    $keys[":".$k] = $v;
                }
                $queryInsert = 'INSERT INTO '.$instancia->parameters['table'];
                $queryInsert.= ' ('.implode(',', array_keys($keysName)).')';
                $queryInsert.= ' VALUES ('.implode(',', array_keys($keys)).')';
                return $queryInsert;
            }
        }
        foreach ($results as $k => $v) {
            $results[$k] = $k."=:".$k;
        }
        $queryUpdate = 'UPDATE '.$instancia->parameters['table'].' SET '.implode(',', $results);
        $queryUpdate.= ' WHERE '.$object->primaryKey.'=:'.$object->primaryKey;
        return $queryUpdate;
    }

    /**
     * Save result from object
     * @param  object $object Results object
     * @return boolean        Returns true or false
     */
    public static function result($object)
    {
        $instancia = new self();
        if ($instancia->connect()) {
            $namespace = get_class($object);
            $instancia->check($namespace);
            $results = $object->toArray();
            $results = self::checkUpdateTime($results, $object, $instancia);
            foreach ($results as $k => $v) {
                $keys[":".$k] = $v;
            }
            try {
                $query = self::query($results, $object, $instancia);
                if (is_null($results[$object->primaryKey]) || empty($results[$object->primaryKey])) {
                    $keys[":id"] = self::createId();
                }
                Debug::logQuery($query);
                $sth = $instancia->connection->prepare($query);
                $sth->execute($keys);
                $results[$object->primaryKey] = $instancia->connection->lastInsertId();
                $object->fromArray($results);
            } catch (\PDOException $e) {
                $instancia->pdoErrors($e);
                return false;
            }
            
            return $object;
        }
        return false;
    }
    
    /**
     * Check for DateTime internal fields
     * @param  array  $results   Results array
     * @param  object $object    Results object
     * @param  onject $instancia Object model instance
     * @return array             Return new datetime for results
     */
    private static function checkUpdateTime($results, $object, $instancia)
    {
        if (\Supernova\Sql\TableInfo::checkField($instancia->parameters['table'], "updated")) {
            $results["updated"] = date('Y-m-d H:i:s');
        }

        if (isset($results[$object->primaryKey])) {
            if (is_null($results[$object->primaryKey]) || empty($results[$object->primaryKey])) {
                if (\Supernova\Sql\TableInfo::checkField($instancia->parameters['table'], "created")) {
                    $results["created"] = date('Y-m-d H:i:s');
                }
            }
        }
        return $results;
    }

    private static function createId()
    {
        $ts = pack('N', time());
        $m = substr(md5(gethostname()), 0, 3);
        $pid = pack('n', getmypid());
        $trail = substr(pack('N', rand()), 1, 3);

        $bin = sprintf("%s%s%s%s", $ts, $m, $pid, $trail);

        $id = '';
        for ($i = 0; $i < 12; $i++) {
            $id .= sprintf("%02X", ord($bin[$i]));
        }
        return $id;
    }
}
