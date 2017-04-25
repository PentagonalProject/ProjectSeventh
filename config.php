<?php
/* ------------------------------------------------------ *\
 |                        CONFIG                          |
\* ------------------------------------------------------ */
return [
    'directory' => [
        'storage' => __DIR__ . DIRECTORY_SEPARATOR . 'Storage',
        'module'  => __DIR__ . DIRECTORY_SEPARATOR . 'Modules',
        'extension' => __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'extensions',
    ],
    'database' => [
        'host'     => 'localhost',
        'user'     => '',
        'password' => '',
        'name'     => '',
        'port'     => 3306,
        'charset'  => 'utf-8',
        'collate'  => 'utf8_unicode_ci',
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
