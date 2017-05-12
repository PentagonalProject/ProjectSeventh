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
    use Slim\Container;

    if (!isset($this) || ! $this instanceof  Arguments) {
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
    $config['httpVersion'] = isset($_SERVER['SERVER_PROTOCOL'])
    && strpos($_SERVER['SERVER_PROTOCOL'], '/') !== false
        ? explode('/', $_SERVER['SERVER_PROTOCOL'])[1]
        : '1.1';

    return function (Container $container) use (&$config) : Config {
        /** @var Application $application */
        $application = $container['application'];
        $config['directory'] = array_merge([
            'extension' => $application->getRootDirectory('Extensions'),
            'module'    => $application->getRootDirectory('Modules'),
            'storage'   => $application->getRootDirectory('Storage'),
        ], $config['directory']);

        return new Config($config);
    };
}
