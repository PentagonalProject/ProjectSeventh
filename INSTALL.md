# INSTALLATION

Please refer [Config/Example.Config.php](Config/Example.Config.php) that contains configuration.

### Requirements

- Php 7 or later
- Php PDO extension (for database)
- Php PCRE extension

### Suggests

- Php cURL Extension (for guzzle)
- Php openssl Extension (for encryption)
- Php mbString extension (for back compatibility)
- Php mbCrypt extension (for back compatibility)
- Php iconv extension (for better sanitation characters conversion)


### NOTE

Public index content is on :

[public/index.php](public/index.php)

That mean your document root must be follow on `public/` directory.

If you want to change structure of `index.php` to your another place / current script directory,
just move the `index.php` & `.htaccess` file.
And open `index.php` and then change.

```
return (new PentagonalProject\ProjectSeventh\Application())
    ->process((array) require __DIR__. '/../Config/Config.php');
```

##

with

```
return (new PentagonalProject\ProjectSeventh\Application())
    ->process((array) require '/path/to/your/Config.php');
```

Just change require Configuration file of `Example.Config.php`, and `Configuration File` must be as contains array return.


##

Or you can fill config inside index.php on process

```
return (new PentagonalProject\ProjectSeventh\Application())
    ->process([
        'directory' => [
        ],
        'database' => [
            'host'     => 'db_host',
            'user'     => 'db_user',
            'password' => 'db_pass',
            'name'     => 'db_name',
            'port'     => 3306,
            'driver'   => 'mysql',
            'charset'  => 'utf8',
            'collate'  => 'utf8_unicode_ci',
            // database prefix
            'prefix'   => 'db_',
        ],
        'environment' => [
            'debug'   => false,
            'log'     => LOG_MODE_NOTICE,
            'error'   => true,
            'log_name' => null,
        ],
        'cache'      => [
            'driver' => 'driverName',
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
            'middleware' => [],
            'routes'  => [
                // example Route
                '/Path/To/RouteFile.php'
            ],
        ]
    ]);
```


### Library Install

Use composer to install, go to script directory and run:

```bash
composer install --no-dev --optimize-autoloader
```

If there was additional file on core directory, please update composer autoload

```bash
composer dump-autoload --optimize-autoloader
```
