<?php
/* ------------------------------------------------------ *\
 |                CONTAINER ERROR HANDLER                 |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'errorHandler'
 */
namespace {

    use Apatis\Exceptions\RuntimeException;
    use PentagonalProject\ProjectSeventh\Hook;
    use PentagonalProject\ProjectSeventh\HttpHandler\Error;
    use Slim\Container;
    use Slim\Handlers\AbstractError;

    /**
     * Error Handler
     *
     * @param Container $container
     * @return AbstractError
     */
    return function (Container $container) : AbstractError {
        /**
         * @var Hook $hook
         */
        $hook = $container[CONTAINER_HOOK];
        $errorHandler = $hook->apply(
            HOOK_HANDLER_ERROR_500,
            new Error(
                $container->get('settings')['displayErrorDetails'],
                $container[CONTAINER_APPLICATION]
            ),
            $container
        );

        if (! $errorHandler instanceof AbstractError) {
            throw new RuntimeException(
                sprintf(
                    "Invalid Hook for Error Handler. Error Handler must be instance of %s",
                    AbstractError::class
                ),
                E_ERROR
            );
        }

        return $errorHandler;
    };
}
