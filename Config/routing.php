<?php

$routing = [

    // Prefijos para las rutas (cual parte de la aplicacion mostrará)
    "prefix" => [
        "Admin",
    ],

    // El orden por defecto de como funcionara la query
    // http://x.com/language/prefix/controler/action/query1/query2/queryN...
    "behaviourOrder" => [
        "language","prefix","controller","action"
    ],

    // Si es falso, solo los "alias" funcionaran
    "actAsBehaviour" => true,

    // Url de fantasia o alias
    "alias" => [
        "/" => [
            "prefix" => null,
            "controller" => "Pages",
            "action" => "Home"
        ]
        // Ejemplo:
        // "/login" => [
        //     "prefix"     => "Admin",
        //     "controller" => "Auth",
        //     "action"     => "Login"
        // ],
    ]

];
