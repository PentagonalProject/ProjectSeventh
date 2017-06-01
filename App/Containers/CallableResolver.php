<?php
/* ------------------------------------------------------ *\
 |             CONTAINER CALLABLE RESOLVER                |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'callableResolver'
 */
namespace {

    use Apatis\Exceptions\RuntimeException;
    use PentagonalProject\ProjectSeventh\Hook;
    use Slim\CallableResolver;
    use Slim\Container;
    use Slim\Interfaces\CallableResolverInterface;

    /**
     * Callable Resolver Container
     *
     * @param Container $container
     * @return CallableResolverInterface
     */
    return function (Container $container) : CallableResolverInterface {
        /**
         * @var Hook $hook
         */
        $hook = $container[CONTAINER_HOOK];
        $callableResolver = $hook->apply(
            HOOK_HANDLER_CALLABLE_RESOLVER,
            new CallableResolver($container),
            $container
        );

        if (! $callableResolver instanceof CallableResolverInterface) {
            throw new RuntimeException(
                sprintf(
                    "Invalid Hook for Callable Resolver. Callable Resolver must be instance of %s",
                    CallableResolverInterface::class
                ),
                E_ERROR
            );
        }

        return $callableResolver;
    };
}
