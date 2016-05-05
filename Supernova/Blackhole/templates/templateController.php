<?php

echo '<?php

namespace App\Controller;

class '.self::$params['plural'].'Controller extends \App\Main
{
    public function executeIndex()
    {
';

if (isset(self::$params['primaryKey'])) {
    echo '      $'.self::$params['plural'].' = \App\Model\\'.self::$params['singular'].'::select()->find();
        self::set(compact("'.self::$params['plural'].'"));';
}
echo '
    }
';
if (isset(self::$params['primaryKey'])) {
    echo '    
    public function executeAdd()
    {
        
    }
    
    public function executeEdit($id = null)
    {
        $'.self::$params['singular'].' = \App\Model\\'.self::$params['singular'].'::select()
            ->where("'.self::$params["primaryKey"].'", "=", $id)
            ->findOne();

        if ($'.self::$params['singular'].') {
            self::set(compact("'.self::$params['singular'].'"));
        } else {
            self::flash(array("message" => __("Data does not exist for this Id"), "status" => "danger"));
            self::redirect(array(
                "prefix" => \Supernova\Core::$elements["prefix"],
                "controller" => "'.self::$params['plural'].'",
                "action" => "index"
            ));
        }
    }
    
    public function executeDelete($id = null)
    {
        if ($id) {
            $'.self::$params['singular'].' = \App\Model\\'.self::$params['singular'].'::select()
                ->where("'.self::$params["primaryKey"].'", "=", $id)
                ->findOne()
                ->remove();
                
            self::flash(array("message" => __("Delete successful"), "status" => "success"));
        } else {
            self::flash(array("message" => __("Data does not exist for this Id"), "status" => "danger"));
        }
        self::redirect(array(
            "prefix" => \Supernova\Core::$elements["prefix"],
            "controller" => "'.self::$params['plural'].'",
            "action" => "index"
        ));
    }';
}
echo "}";
