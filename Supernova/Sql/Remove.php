<?php

namespace Supernova\Sql;

use \Supernova\Debug as Debug;

class Remove extends \Supernova\Sql
{
    /**
     * Query remove
     * @param  array $results   Results array
     * @param  object $object    Results object
     * @param  object $instancia Model object instance
     * @return boolean            Returns true or false
     */
    private static function query($results, $object, $instancia)
    {
        if (isset($results[$object->primaryKey])) {
            $queryRemove = 'DELETE FROM '.$instancia->parameters['table'];
            $queryRemove.= ' WHERE `'.$object->primaryKey.'`=\''.$results[$object->primaryKey].'\'';
            return $queryRemove;
        }
        return false;
    }

    /**
     * Remove result from database
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
            try {
                $query = self::query($results, $object, $instancia);
                Debug::logQuery($query);
                $sth = $instancia->connection->prepare($query);
                $sth->execute();
            } catch (\PDOException $e) {
                $instancia->pdoErrors($e);
                return false;
            }
            return $object;
        }
        return false;
    }
}
