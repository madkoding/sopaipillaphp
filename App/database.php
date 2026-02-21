<?php

/**
 * Database configuration.
 *
 * Values are read from environment variables (loaded from .env by Sopaipilla\Env).
 * Change DB_CONNECTION in your .env to switch between sqlite and mysql.
 */

use Sopaipilla\Env;

return [
    'default' => Env::get('DB_CONNECTION', 'sqlite'),

    'connections' => [
        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => Env::get('DB_DATABASE', ':memory:'),
        ],

        'mysql' => [
            'driver'   => 'mysql',
            'host'     => Env::get('DB_HOST', '127.0.0.1'),
            'port'     => Env::get('DB_PORT', '3306'),
            'database' => Env::get('DB_NAME', 'sopaipilla'),
            'username' => Env::get('DB_USERNAME', 'root'),
            'password' => Env::get('DB_PASSWORD', ''),
            'charset'  => Env::get('DB_CHARSET', 'utf8mb4'),
        ],
    ],
];