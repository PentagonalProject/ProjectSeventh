<?php
/* ------------------------------------------------------ *\
 |                        CONFIG                          |
\* ------------------------------------------------------ */
/**
 * Use @const WEB_ROOT to get Public / Web Directory
 */
return [
    'directory' => [
        'storage'   => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Storage',
        'module'    => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Modules',
        'extension' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Extensions',
        // load from example
        // 'extension' => __DIR__ . DIRECTORY_SEPARATOR . '_example/Extensions',
        // 'module'    => __DIR__ . DIRECTORY_SEPARATOR . '_example/Modules',
    ],
    'database' => [
        'host'     => 'localhost',
        'user'     => '',
        'password' => '',
        'name'     => '',
        'port'     => 3306,
        'driver'   => 'mysql',
        'charset'  => 'utf8',
        'collate'  => 'utf8_unicode_ci',
        // database prefix
        'prefix'   => 'db_',
    ],
    'environment' => [
        // beware debug log will be make your disk full, use this for dev only
        'debug'   => true,
        // set Notice for log
        'log'     => LOG_MODE_NOTICE,
        // show error verbose
        'error'   => true,
        // logs.log if there / or \ will be convert to underscore
        // log placed under directory[storage]/logs (Default 'Storage/logs')
        // and set null or empty value to make log automatically set by log level
        'log_name' => null,
    ],
    'cache'      => [
        // driver name
        'driver' => 'auto',
        /**
         * @see \phpFastCache\CacheManager::getDefaultConfig()
         */
        'config' => [
            'securityKey' => 'auto',
            'ignoreSymfonyNotice' => false,
            'defaultTtl' => 900,
            'htaccess' => true,
            'default_chmod' => 0777,
            'path' => '',
            'fallback' => false,
            'limited_memory_each_object' => 4096,
            'compress_data' => false,
        ]
    ],
    'session'  => [
        'name' => null,
        'save_path' => null,
        // values of cookie params
        'path' => '/',
        'lifetime' => 0,
        'domain'   => null,
        'httponly' => null,
        'secure'   => null,
    ],
    // auto loading on separate loaded init
    'autoload' => [
        // load middle ware end of middle ware init
        'middleware' => [
        ],
        // load on routes
        'routes'  => [
            // example Route
            __DIR__ . '/Example.Routes.php'
        ],
    ],
];
