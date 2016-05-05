<?php
define('HTACCESS', false); // cuando mod_rewrite esta inactivo en apache queda falso
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', str_replace('\\', DS, dirname(__FILE__)));
define('CORE', ROOT.DS."Supernova".DS);

include(CORE."bootstrap.php");
