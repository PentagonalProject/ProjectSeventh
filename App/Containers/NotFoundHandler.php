<?php
/* ------------------------------------------------------ *\
 |              CONTAINER NOT FOUND HANDLER               |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'notFoundHandler'
 */
namespace {

    use Apatis\Exceptions\RuntimeException;
    use PentagonalProject\ProjectSeventh\Hook;
    use Slim\Container;
    use Slim\Handlers\AbstractHandler;
    use Slim\Handlers\NotFound;

    /**
     * Not Found Handler
     *
     * @param Container $container
     * @return AbstractHandler
     */
    return function (Container $container) : AbstractHandler {
        /**
         * @var Hook $hook
         */
        $hook = $container[CONTAINER_HOOK];
        $notFoundHandler = $hook->apply(
            HOOK_HANDLER_ERROR_404,
            new NotFound(),
            $container
        );

        if (! $notFoundHandler instanceof AbstractHandler) {
            throw new RuntimeException(
                sprintf(
                    "Invalid Hook for Not Found Handler. Not Found Handler must be instance of %s",
                    AbstractHandler::class
                ),
                E_ERROR
            );
        }

        return $notFoundHandler;
    };
}
