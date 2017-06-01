<?php
/* ------------------------------------------------------ *\
 |                   CONTAINER CONFIG                     |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'config'
 * Stored Config
 */
namespace {

    use PentagonalProject\ProjectSeventh\Application;
    use PentagonalProject\ProjectSeventh\Arguments;
    use PentagonalProject\ProjectSeventh\Config;
    use PentagonalProject\ProjectSeventh\Hook;
    use phpFastCache\CacheManager;
    use Slim\Container;
    use Slim\Http\Response;

    if (!isset($this) || ! $this instanceof Arguments) {
        return;
    }

    if (count($this) < 1) {
        return;
    }

    $config = (array) $this[1];
    $config['directory'] = isset($config['directory']) ? $config['directory'] : [];
    if (!is_array($config['directory'])) {
        $config['directory'] =  [];
    }

    // override Environment as Array
    if (!isset($config['environment']) || !is_array($config['environment'])) {
        $config['environment'] = [];
    }

    // auto httpVersion
    $config['environment']['httpVersion'] = isset($_SERVER['SERVER_PROTOCOL'])
    && strpos($_SERVER['SERVER_PROTOCOL'], '/') !== false
        ? explode('/', $_SERVER['SERVER_PROTOCOL'])[1]
        : '1.1';

    return function (Container $container) use (&$config) : Config {
        // override Server Protocol
        $container['settings']['httpVersion'] = $config['environment']['httpVersion'];
        /** @var Response $response */
        $response = clone $container[CONTAINER_RESPONSE];
        if ($response->getProtocolVersion() != $config['environment']['httpVersion']) {
            unset($container[CONTAINER_RESPONSE]);
            try {
                $newResponse = $response->withProtocolVersion($config['httpVersion']);
                $container[CONTAINER_RESPONSE] = $newResponse;
            } catch (\Exception $exception) {
                $container[CONTAINER_RESPONSE] = $response;
            }
        }
        /** @var Application $application */
        $application = $container[CONTAINER_APPLICATION];
        $config['directory'] = array_merge([
            'extension' => $application->getRootDirectory('Extensions'),
            'module'    => $application->getRootDirectory('Modules'),
            'storage'   => $application->getRootDirectory('Storage'),
        ], $config['directory']);
        if (!isset($config['cache']) || !is_array($config['cache'])) {
            $config['cache'] = [
                'driver' => 'Auto',
                'config' => CacheManager::getDefaultConfig()
            ];
        }

        if (!is_string($config['cache']['driver']) || trim($config['cache']['driver']) == '') {
            $config['cache']['driver'] = 'Auto';
        }
        $config['cache']['driver'] = CacheManager::standardizeDriverName($config['cache']['driver']);
        if (!in_array($config['cache']['driver'], CacheManager::getStaticAllDrivers())) {
            $config['cache']['driver'] = 'Auto';
        }

        if (!is_array($config['cache']['config'])) {
            $config['cache']['config'] = CacheManager::getDefaultConfig();
        }
        /**
         * @var Hook $hook
         */
        $hook = $container[CONTAINER_HOOK];
        $cacheDriver = $hook->apply(HOOK_HANDLER_CACHE_DRIVER, $config['cache']['driver']);
        $cacheConfig = $hook->apply(HOOK_HANDLER_CACHE_CONFIG, $config['cache']['config']);
        $cacheDriver = !is_string($cacheDriver)
            ? 'Auto'
            : CacheManager::standardizeDriverName($cacheDriver);
        $cacheConfig = !is_array($cacheConfig)
            ? CacheManager::getDefaultConfig()
            : $cacheConfig;

        if (!$cacheDriver || !in_array($cacheDriver, CacheManager::getStaticAllDrivers())) {
            $cacheDriver = 'Auto';
        }
        $config['cache']['driver'] = $cacheDriver;
        $config['cache']['config'] = $cacheConfig;
        $config = new Config($config);
        /**
         * Config Override
         */
        $container['settings']['displayErrorDetails'] = (bool) $config['environment[error]'];
        $container['settings']['addContentLengthHeader'] = ! isset($config['environment[addContentLengthHeader]'])
            ? true
            : (bool) $config['environment[addContentLengthHeader]'];
        return $config;
    };
}
