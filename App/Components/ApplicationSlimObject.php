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
    use Slim\Http\Environment;

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
            }
        ]
    );
}
