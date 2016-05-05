<?php

/**
 * Require autoload
 */
require_once CORE."Autoload.php";

/**
 * Require global functions
 */
require_once CORE."globalFunctions.php";

/**
 * Enable autoload
 * @var function
 */
$autoLoad = new \Supernova\AutoLoad();
$autoLoad->register();

\Supernova\Core\Config::setErrorHandler();
\Supernova\Profiler::start();
\Supernova\Core\Check::modules();
\Supernova\Core\Config::setup();
\Supernova\Core::initialize();
\Supernova\Route::initialize();
\Supernova\Core\Request::setup();
\Supernova\Core\Elements::setup();
\Supernova\Core\Check::controller();

\Supernova\Model\Process::form();

$mainAppController = new \App\Main();
$mainAppController->beforeController();
$mainController = new \Supernova\Controller();
\Supernova\Core\Check::action();
$mainAppController->afterController();
\Supernova\View::render();
