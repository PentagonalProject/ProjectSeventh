<?php
/* ------------------------------------------------------ *\
 |              CONTAINER PHP ERROR HANDLER               |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'phpErrorHandler'
 */
namespace {

    use Apatis\Exceptions\RuntimeException;
    use PentagonalProject\ProjectSeventh\Hook;
    use PentagonalProject\ProjectSeventh\HttpHandler\PhpError;
    use Slim\Container;
    use Slim\Handlers\AbstractError;

    /**
     * Php Error Handler
     *
     * @param Container $container
     * @return AbstractError
     */
    return function (Container $container) : AbstractError {
        /**
         * @var Hook $hook
         */
        $hook = $container[CONTAINER_HOOK];
        $errorPhpHandler = $hook->apply(
            HOOK_HANDLER_ERROR_PHP,
            new PhpError(
                $container->get('settings')['displayErrorDetails'],
                $container[CONTAINER_APPLICATION]
            ),
            $container
        );

        if (! $errorPhpHandler instanceof AbstractError) {
            throw new RuntimeException(
                sprintf(
                    "Invalid Hook for Php Error Handler. Php Error Handler must be instance of %s",
                    AbstractError::class
                ),
                E_ERROR
            );
        }

        return $errorPhpHandler;
    };
}
