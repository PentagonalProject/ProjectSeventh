<?php
/* ------------------------------------------------------ *\
 |                CONTAINER FOUND HANDLER                 |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'foundHandler'
 */
namespace {

    use Apatis\Exceptions\RuntimeException;
    use PentagonalProject\ProjectSeventh\Hook;
    use Slim\Container;
    use Slim\Handlers\Strategies\RequestResponse;
    use Slim\Interfaces\InvocationStrategyInterface;

    /**
     * Found Handler
     *
     * @param Container $container
     * @return InvocationStrategyInterface
     */
    return function (Container $container) : InvocationStrategyInterface {
        /**
         * @var Hook $hook
         */
        $hook = $container[CONTAINER_HOOK];
        $callableResolver = $hook->apply(
            HOOK_HANDLER_FOUND_RESPONSE,
            new RequestResponse(),
            $container
        );
        if (! $callableResolver instanceof InvocationStrategyInterface) {
            throw new RuntimeException(
                sprintf(
                    "Invalid Hook for Found Handler. Found Handler must be instance of %s",
                    InvocationStrategyInterface::class
                ),
                E_ERROR
            );
        }

        return $callableResolver;
    };
}
