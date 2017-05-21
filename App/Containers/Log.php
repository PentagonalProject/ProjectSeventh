<?php
/* ------------------------------------------------------ *\
 |                    CONTAINER LOG                      |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'log'
 */
namespace {

    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;
    use PentagonalProject\ProjectSeventh\Application;
    use PentagonalProject\ProjectSeventh\Config;
    use Slim\Container;

    /**
     * @param Container $container
     * @return Logger
     */
    return function (Container $container) : Logger {
        $logger = new Logger('default');
        /** @var Config $config */
        $config = $container[CONTAINER_CONFIG];
        if ($config['environment[log]']) {
            $type = is_int($config['environment[log]'])
                ? $config['environment[log]']
                : ($config['environment[debug]']
                    ? Logger::DEBUG
                    : Logger::WARNING
                );
            $logName = $config['environment[log_name]'];
            if (!is_string($logName)) {
                $logName = 'log.log';
            }
            $logName = str_replace(
                [
                    '/',
                    '\\'
                ],
                '_',
                $logName
            );
            $config['environment[log_name]'] = $logName;
            /**
             * @var Application $app
             */
            $app = $container[CONTAINER_APPLICATION];
            $logName = $app->getFixPath(
                $config['directory[storage]']
                . '/logs/'
                . $logName
            );

            $logger->pushHandler(new StreamHandler(
                $logName,
                $type
            ));
        }
        return $logger;
    };
}
