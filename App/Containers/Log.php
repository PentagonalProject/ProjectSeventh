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
                    ? LOG_MODE_DEFAULT
                    : Logger::WARNING
                );
            $type = getAliasLogLevel($type);
            $type = $type > 0 ? $type : 0;
        }

        if (!empty($type)) {
            $logName = $config['environment[log_name]'];
            $hasLog = true;
            if (!$logName || !is_string($logName) || trim($logName) == '') {
                $hasLog = false;
                $logName = getDefaultLogNameByCode($type, 'logs.log');
            }

            $logName = $hasLog ? preg_replace(
                '/(\\\|\/)/',
                DIRECTORY_SEPARATOR,
                $logName
            ) : $logName;

            $selfLog = ! ($hasLog);
            if (!$selfLog && (
                DIRECTORY_SEPARATOR == '/' && $logName[0] === DIRECTORY_SEPARATOR ||
                DIRECTORY_SEPARATOR == '\\' && preg_match('/^([a-z]+)\:\\/i', $logName)
                )
            ) {
                $selfLog = file_exists($logName)
                    || (dirname($logName) !== DIRECTORY_SEPARATOR
                        && is_dir(dirname($logName))
                        && is_writable(dirname($logName))
                    );
            }

            if (! $selfLog) {
                $logName = str_replace(
                    [
                        '/',
                        '\\'
                    ],
                    '_',
                    $logName
                );

                /**
                 * @var Application $app
                 */
                $app = $container[CONTAINER_APPLICATION];
                $logName = $app->getFixPath(
                    $config['directory[storage]']
                    . '/logs/'
                    . $logName
                );
            }

            $config['environment[log_name]'] = $logName;
            $logger->pushHandler(new StreamHandler(
                $logName,
                $type
            ));
        }

        return $logger;
    };
}
