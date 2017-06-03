<?php
/* ------------------------------------------------------ *\
 |          APPLICATION COMPONENT SLIM OBJECT             |
\* ------------------------------------------------------ */

namespace {

    use PentagonalProject\ProjectSeventh\Application;
    use PentagonalProject\ProjectSeventh\Arguments;
    use PentagonalProject\ProjectSeventh\Config;
    use PentagonalProject\ProjectSeventh\Database;
    use PentagonalProject\ProjectSeventh\Hook;
    use PentagonalProject\ProjectSeventh\Session;
    use PentagonalProject\ProjectSeventh\Utilities\EmbeddedCollection;
    use Slim\App;
    use Slim\Container;
    use Slim\Handlers\AbstractError;
    use Slim\Handlers\AbstractHandler;
    use Slim\Http\Environment;
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

    /**
     * @return \Slim\App
     */
    return new App(
        [
            /**
             * Application Instance
             *
             * Use on closure prevent being binding to
             * @return Application
             */
            CONTAINER_APPLICATION => function () use (&$app) : Application {
                return $app;
            },
            /**
             * Configuration Container
             *
             * Use on closure prevent being binding to
             * @return Config
             */
            CONTAINER_CACHE => $app->includeScope($app->getContainerDirectory('Cache.php')),
            /**
             * Configuration Container
             *
             * Use on closure prevent being binding to
             * @return Config
             */
            CONTAINER_CONFIG => $app->includeScope($app->getContainerDirectory('Config.php'), $this[1]),
            /**
             * Closure
             *
             * @return Database
             */
            CONTAINER_DATABASE => $app->includeScope($app->getContainerDirectory('Database.php')),
            /**
             * Closure
             *
             * @return Environment
             */
            CONTAINER_ENVIRONMENT => $app->includeScope($app->getContainerDirectory('Environment.php')),
            /**
             * Extension Container
             *
             * Closure
             *
             * @return EmbeddedCollection
             */
            CONTAINER_EXTENSION => $app->includeScope($app->getContainerDirectory('Extension.php')),
            /**
             * Closure
             *
             * @return Hook
             */
            CONTAINER_HOOK => $app->includeScope($app->getContainerDirectory('Hook.php')),
            CONTAINER_LOG => $app->includeScope($app->getContainerDirectory('Log.php')),
            /**
             * Module Container
             *
             * Closure
             *
             * @return EmbeddedCollection
             */
            CONTAINER_MODULE => $app->includeScope($app->getContainerDirectory('Module.php')),
            /**
             * @return array
             */
            CONTAINER_SETTINGS => $app->includeScope($app->getContainerDirectory('Settings.php')),
            /**
             * Session Container
             *
             * Closure
             *
             * @return Session
             */
            CONTAINER_SESSION => $app->includeScope($app->getContainerDirectory('Session.php')),
            /**
             * Slim Inheritance
             *
             * @return App
             */
            CONTAINER_SLIM => function (Container $container) : App {
                /**
                 * @var Application $application
                 */
                $application =& $container[CONTAINER_APPLICATION];
                return $application->getSlim();
            },
            /**
             * Callable Resolver
             *
             * @return CallableResolverInterface
             */
            CONTAINER_CALLABLE_RESOLVER => $app->includeScope($app->getContainerDirectory('CallableResolver.php')),
            /**
             * Found Handler
             *
             * @return InvocationStrategyInterface
             */
            CONTAINER_FOUND_HANDLER => $app->includeScope($app->getContainerDirectory('FoundHandler.php')),
            /**
             * Not Found Handler
             *
             * @return AbstractHandler
             */
            CONTAINER_NOT_FOUND_HANDLER => $app->includeScope($app->getContainerDirectory('NotFoundHandler.php')),
            /**
             * Not Allowed Handler
             *
             * @return AbstractHandler
             */
            CONTAINER_NOT_ALLOWED_HANDLER => $app->includeScope($app->getContainerDirectory('NotAllowedHandler.php')),
            /**
             * Error Handler
             *
             * @return AbstractError
             */
            CONTAINER_ERROR_HANDLER => $app->includeScope($app->getContainerDirectory('ErrorHandler.php')),
            /**
             * Php Error Handler
             *
             * @return AbstractError
             */
            CONTAINER_PHP_ERROR_HANDLER => $app->includeScope($app->getContainerDirectory('PhpErrorHandler.php')),
            /**
             * ShutDown Handler
             *
             * @return mixed
             */
            CONTAINER_SHUTDOWN => $app->includeScope($app->getContainerDirectory('ShutdownHandler.php')),
        ]
    );
}
