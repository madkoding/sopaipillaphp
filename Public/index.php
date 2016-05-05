<?php
define('HTACCESS', true); // cuando mod_rewrite esta activo
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', str_replace('\\', DS, dirname(dirname(__FILE__))));
define('CORE', ROOT.DS."Supernova".DS);

include(CORE."bootstrap.php");
