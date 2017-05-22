<?php
/* ------------------------------------------------------ *\
 |             APPLICATION COMPONENT MIDDLEWARE            |
\* ------------------------------------------------------ */

namespace {

    use Apatis\Exceptions\RuntimeException;
    use PentagonalProject\ProjectSeventh\Application;
    use PentagonalProject\ProjectSeventh\Arguments;
    use PentagonalProject\ProjectSeventh\Config;
    use PentagonalProject\ProjectSeventh\Hook;
    use PentagonalProject\ProjectSeventh\HttpHandler\PhpError;
    use PentagonalProject\ProjectSeventh\HttpHandler\Error;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Slim\CallableResolver;
    use Slim\Container;
    use Slim\Handlers\AbstractError;
    use Slim\Handlers\AbstractHandler;
    use Slim\Handlers\NotAllowed;
    use Slim\Handlers\NotFound;
    use Slim\Handlers\Strategies\RequestResponse;
    use Slim\Http\Uri;
    use Slim\Interfaces\CallableResolverInterface;
    use Slim\Interfaces\InvocationStrategyInterface;

    if (!isset($this) || ! $this instanceof Arguments) {
        return;
    }

    /** @var Application $app */
    $app =& $this[CONTAINER_APPLICATION];
    if (!$app instanceof Application) {
        return;
    }

    $slim =& $app->getSlim();

    /**
     * MiddleWare Fix Uri
     *
     * That means Duplicate URI Indexing
     * /index.php/(REQUEST_URI) ==== /(REQUEST_URI)
     * & Remark of ErrorDetails Setting
     */
    $slim->add(function (ServerRequestInterface $request, ResponseInterface $response, $next) {
        /**
         * Remark Display Error Details
         *
         * @var Config $config
         */
        $config = $this[CONTAINER_CONFIG];
        $this[CONTAINER_SETTING]['displayErrorDetails'] = (bool) (
            $config->get('environment[debug]', false) || $config->get('environment[error]', false)
        );

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
                    new CallableResolver($this),
                    $this
                );
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
                    new RequestResponse,
                    $this
                );
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
                $notFoundHandler = $hook->apply(
                    HOOK_HANDLER_ERROR_404,
                    new NotFound,
                    $this
                );
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
            $notAllowedHandler = $hook->apply(
                HOOK_HANDLER_ERROR_403,
                new NotAllowed,
                $this
            );
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
                    new Error(
                        $this->get('settings')['displayErrorDetails'],
                        $this[CONTAINER_APPLICATION]
                    ),
                    $this
                );
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
                    new PhpError(
                        $this->get('settings')['displayErrorDetails'],
                        $this[CONTAINER_APPLICATION]
                    ),
                    $this
                );
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

    /**
     * Includes Middleware auto load from config
     */

    /**
     * @var Config $config
     */
    $config = $app[CONTAINER_CONFIG];
    if (($middleWares = $config['autoload[middleware]']) && is_array($middleWares)) {
        $c = 0;
        foreach ($middleWares as $middleWare) {
            if (is_string($middleWare) && file_exists($middleWare)) {
                $app->includeScope($middleWare);
                $c++;
            }
        }

        ($c > 0) &&
            $app[CONTAINER_LOG]->debug('Additional Middleware initiated', [
                'Count' => $c
            ]);
    }
}
