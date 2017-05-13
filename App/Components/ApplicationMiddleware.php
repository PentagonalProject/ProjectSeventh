<?php
declare(strict_types=1);

/* ------------------------------------------------------ *\
 |             APPLICATION COMPONENT MIDDLEWARE            |
\* ------------------------------------------------------ */

namespace {

    use Apatis\Exceptions\RuntimeException;
    use PentagonalProject\ProjectSeventh\Application;
    use PentagonalProject\ProjectSeventh\Arguments;
    use PentagonalProject\ProjectSeventh\Hook;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Slim\CallableResolver;
    use Slim\Container;
    use Slim\Handlers\AbstractError;
    use Slim\Handlers\AbstractHandler;
    use Slim\Handlers\NotAllowed;
    use Slim\Handlers\NotFound;
    use Slim\Handlers\Error;
    use Slim\Handlers\PhpError;
    use Slim\Handlers\Strategies\RequestResponse;
    use Slim\Http\Uri;
    use Slim\Interfaces\CallableResolverInterface;
    use Slim\Interfaces\InvocationStrategyInterface;

    if (!isset($this) || ! $this instanceof Arguments) {
        return;
    }

    /** @var Application $c */
    $c =& $this[CONTAINER_APPLICATION];
    if (!$c instanceof Application) {
        return;
    }

    $slim =& $c->getSlim();

    /**
     * MiddleWare FIX Uri
     * That means Duplicate URI Indexing
     * /index.php/(REQUEST_URI) ==== /(REQUEST_URI)
     */
    $slim->add(function (ServerRequestInterface $request, ResponseInterface $response, $next) {
        /**
         * @var Uri $uri
         */
        $uri = $request->getUri();
        $serverParams = $request->getServerParams();
        if (isset($serverParams['SCRIPT_NAME'])
            && $uri->getBasePath() === $serverParams['SCRIPT_NAME']
        ) {
            $request = $request->withUri($uri->withBasePath(dirname($uri->getBasePath())));
        }
        return $next($request, $response);
    });

    /**
     * Middle Ware With Handler
     */
    $slim->add(function (ServerRequestInterface $request, ResponseInterface $response, $next) {
        if (!isset($this[CONTAINER_CALLABLE_RESOLVER])) {
            /**
             * Callable Resolver
             *
             * @var $this Container
             * @return AbstractHandler
             */
            $this[CONTAINER_CALLABLE_RESOLVER] = function () {
                /**
                 * @var Hook $hook
                 */
                $hook = $this[CONTAINER_HOOK];
                $callableResolver = $hook->apply(
                    HOOK_HANDLER_CALLABLE_RESOLVER,
                    function (Container $container) {
                        return new CallableResolver($container);
                    },
                    $this
                );
                $callableResolver = $callableResolver($this);
                if (!$callableResolver instanceof CallableResolverInterface) {
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

        if (!isset($this[CONTAINER_FOUND_HANDLER])) {
            /**
             * Found Handler
             *
             * @var $this Container
             * @return AbstractHandler
             */
            $this[CONTAINER_FOUND_HANDLER] = function () {
                /**
                 * @var Hook $hook
                 */
                $hook = $this[CONTAINER_HOOK];
                $callableResolver = $hook->apply(
                    HOOK_HANDLER_FOUND_RESPONSE,
                    function () {
                        return new RequestResponse;
                    },
                    $this
                );
                $callableResolver = $callableResolver($this);
                if (!$callableResolver instanceof InvocationStrategyInterface) {
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

        if (!isset($this[CONTAINER_NOT_FOUND_HANDLER])) {
            /**
             * Not Found Handler
             *
             * @var $this Container
             * @return AbstractHandler
             */
            $this[CONTAINER_NOT_FOUND_HANDLER] = function () {
                /**
                 * @var Hook $hook
                 */
                $hook = $this[CONTAINER_HOOK];
                $notFoundHandler = $hook->apply(HOOK_HANDLER_ERROR_404, function () {
                    return new NotFound();
                }, $this);
                $notFoundHandler = $notFoundHandler($this);
                if (!$notFoundHandler instanceof AbstractHandler) {
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

        /**
         * Not Allowed Handler
         *
         * @var $this Container
         * @return AbstractHandler
         */
        $this[CONTAINER_NOT_ALLOWED_HANDLER] = function () {
            /**
             * @var Hook $hook
             */
            $hook = $this[CONTAINER_HOOK];
            $notAllowedHandler = $hook->apply(HOOK_HANDLER_ERROR_403, function () {
                return new NotAllowed();
            }, $this);
            $notAllowedHandler = $notAllowedHandler($this);
            if (!$notAllowedHandler instanceof AbstractHandler) {
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
        if (!isset($this[CONTAINER_ERROR_HANDLER])) {
            /**
             * Error Handler
             *
             * @var $this Container
             * @return AbstractHandler
             */
            $this[CONTAINER_ERROR_HANDLER] = function () {
                /**
                 * @var Hook $hook
                 */
                $hook = $this[CONTAINER_HOOK];
                $errorHandler = $hook->apply(
                    HOOK_HANDLER_ERROR_500,
                    function (Container $container) {
                        return new Error($container->get('settings')['displayErrorDetails']);
                    },
                    $this
                );
                $errorHandler = $errorHandler($this);
                if (!$errorHandler instanceof AbstractError) {
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

        if (!isset($this[CONTAINER_PHP_ERROR_HANDLER])) {
            /**
             * Php Error Handler
             *
             * @var $this Container
             * @return AbstractHandler
             */
            $this[CONTAINER_PHP_ERROR_HANDLER] = function () {
                /**
                 * @var Hook $hook
                 */
                $hook = $this[CONTAINER_HOOK];
                $errorPhpHandler = $hook->apply(
                    HOOK_HANDLER_ERROR_PHP,
                    function (Container $container) {
                        return new PhpError($container->get('settings')['displayErrorDetails']);
                    },
                    $this
                );
                $errorPhpHandler = $errorPhpHandler($this);
                if (!$errorPhpHandler instanceof AbstractError) {
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

        return $next($request, $response);
    });
}
