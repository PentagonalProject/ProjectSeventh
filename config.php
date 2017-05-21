<?php
/* ------------------------------------------------------ *\
 |                        CONFIG                          |
\* ------------------------------------------------------ */
/**
 * Use @const WEB_ROOT to get Public / Web Directory
 */
return [
    'directory' => [
        'storage'   => __DIR__ . DIRECTORY_SEPARATOR . 'Storage',
        'module'    => __DIR__ . DIRECTORY_SEPARATOR . 'Modules',
        'extension' => __DIR__ . DIRECTORY_SEPARATOR . 'Extensions',
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
    ],
    'environment' => [
        'debug'   => true,
        'error'   => true,
        'log'     => true,
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
        // load on inside middleware before another autoload middle ware loaded
        'modules' => [
        ],
        // load on inside middleware before another autoload middle ware loaded
        // after modules
        'extensions' => [
        ],
        // load middle ware end of middle ware init
        'middleware' => [
        ],
        // load on routes
        'routes'  => [
        ],
    ],
];
