<?php

namespace Supernova\Blackhole;

use \Supernova\Inflector\Pluralize as Pluralize;
use \Supernova\Inflector\Singularize as Singularize;
use \Supernova\Inflector\Underscore as Underscore;
use \Supernova\Blackhole\Keys as Keys;

class Generator
{
    /**
     * Parameters for generator
     * @var array
     */
    public static $params = array();
    
    /**
     * Initialize generator
     * @param string $modelName Camelized model name
     */
    public function __construct($modelName)
    {
        self::$params["modelName"] = $modelName;
        self::$params["path"] = ROOT.DS."App".DS;
        self::$params["templatesPath"] = ROOT.DS."Supernova".DS."Blackhole".DS."templates";
        self::$params["plural"] = Pluralize::word(self::$params["modelName"]);
        self::$params["singular"] = Singularize::word(self::$params["modelName"]);
        return ($this->checkFields()) ? $this : false;
    }

    /**
     * Check fields for parameters
     * @return null
     */
    private function checkFields()
    {
        $table = Underscore::word(self::$params['plural']);
        self::$params['fields'] = \Supernova\Sql\TableInfo::getFields($table);
        if (self::$params['fields']) {
            Keys::setPrimary();
            Keys::setDefault();
            return true;
        } else {
            return false;
        }
    }
}
