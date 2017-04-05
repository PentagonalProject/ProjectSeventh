<?php
/* ------------------------------------------------------ *\
 |                        CONFIG                          |
\* ------------------------------------------------------ */
return [
    'directory' => [
        'storage' => __DIR__ . DIRECTORY_SEPARATOR . 'Storage',
        'app'     => __DIR__ . DIRECTORY_SEPARATOR . 'App',
        'module'  => __DIR__ . DIRECTORY_SEPARATOR . 'Modules',
    ],
    'database' => [
        'host'     => 'localhost',
        'user'     => '',
        'password' => '',
        'name'     => '',
        'port'     => 3306,
        'charset'  => 'utf-8',
    ],
    'environment' => [
        'debug'   => true,
        'error'   => true,
        'log'     => true,
    ],
    'session'  => [
        'path' => '/',
        'save_path' => null,
        'expire' => null,
        'domain' => '*',
    ],
];
