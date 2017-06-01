<?php
/* ------------------------------------------------------ *\
 |             CONTAINER NOT ALLOWED HANDLER              |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'notAllowedHandler'
 */
namespace {

    use Apatis\Exceptions\RuntimeException;
    use PentagonalProject\ProjectSeventh\Hook;
    use Slim\Container;
    use Slim\Handlers\AbstractHandler;
    use Slim\Handlers\NotAllowed;

    /**
     * Not Allowed Handler
     *
     * @param Container $container
     * @return AbstractHandler
     */
    return function (Container $container) : AbstractHandler {
        /**
         * @var Hook $hook
         */
        $hook = $container[CONTAINER_HOOK];
        $notAllowedHandler = $hook->apply(
            HOOK_HANDLER_ERROR_403,
            new NotAllowed(),
            $container
        );

        if (! $notAllowedHandler instanceof AbstractHandler) {
            throw new RuntimeException(
                sprintf(
                    "Invalid Hook for Not Allowed Handler. Not Allowed Handler must be instance of %s",
                    AbstractHandler::class
                ),
                E_ERROR
            );
        }

        return $notAllowedHandler;
    };
}
