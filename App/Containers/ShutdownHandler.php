<?php
/* ------------------------------------------------------ *\
 |             CONTAINER SHUTDOWN HANDLER                 |
\* ------------------------------------------------------ */

namespace {

    use Psr\Container\ContainerInterface;

    /**
     * Register Shutdown
     * @param ContainerInterface $container
     * @return \Callable
     */
    return function (ContainerInterface $container) {
        return function () use ($container) {
            /**
             * @var \Monolog\Logger $logger
             */
            $logger = $container[CONTAINER_LOG];
            $lastError = error_get_last();
            // log when only error Get
            if (!empty($lastError) && $lastError['type'] == E_ERROR) {
                // log the error
                $logger->error(
                    $lastError['message'],
                    $lastError
                );
            }
        };
    };
}
