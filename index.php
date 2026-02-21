<?php

declare(strict_types=1);

// PSR-4 autoloader — maps Sopaipilla\ and App\ to their directories
spl_autoload_register(function (string $class): void {
    $map = [
        'Sopaipilla\\' => __DIR__ . '/Sopaipilla/',
        'App\\'        => __DIR__ . '/App/',
    ];

    foreach ($map as $prefix => $baseDir) {
        if (!str_starts_with($class, $prefix)) {
            continue;
        }
        $relative = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Environment — load before anything else so all config reads env vars
use Sopaipilla\Env;
Env::load(__DIR__ . '/.env');

// Security — sanitize all superglobals and validate the HTTP method
use Sopaipilla\Security\Security;
Security::cleanAll();

// Router — register controllers and dispatch the current request
use Sopaipilla\Routing\Router;
use App\AppController;
use App\Users\UsersController;

$router = new Router();
$router->registerController(new AppController());
$router->registerController(new UsersController());
$router->dispatch();
