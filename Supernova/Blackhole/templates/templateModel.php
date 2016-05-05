<?php

echo '<?php

namespace App\Model;

class '.self::$params['singular'].' extends \Supernova\Model
{
    public $primaryKey = "'.self::$params['primaryKey'].'";
    public $defaultKey = "'.self::$params['defaultKey'].'";
}
';
