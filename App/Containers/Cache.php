<?php
/* ------------------------------------------------------ *\
 |                   CONTAINER CACHE                      |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'database'
 * Handle Database abstraction stored object (PDO)
 */
namespace {

    use PentagonalProject\ProjectSeventh\Config;
    use phpFastCache\Cache\ExtendedCacheItemPoolInterface;
    use phpFastCache\CacheManager;
    use phpFastCache\Exceptions\phpFastCacheDriverCheckException;
    use Slim\Container;

    return function (Container $container) : ExtendedCacheItemPoolInterface {
        /**
         * @var Config $config
         */
        $config = $container[CONTAINER_CONFIG];
        // add fix to prevent trigger error
        if ($config['cache[driver]'] == 'Auto') {
            $availableDriver = CacheManager::getStaticSystemDrivers();
            foreach ($availableDriver as $driver) {
                try {
                    CacheManager::getInstance($driver, $config['cache[config]']);
                    $config['cache[driver]'] = $driver;
                    break;
                } catch (phpFastCacheDriverCheckException $e) {
                    continue;
                }
            }
        }

        $cache = CacheManager::getInstance(
            $config['cache[driver]'],
            $config['cache[config]']
        );

        $container[CONTAINER_LOG]->debug(
            'Cache initiated',
            [
                'Driver' => $config['cache[driver]']
            ]
        );
        return $cache;
    };
}
