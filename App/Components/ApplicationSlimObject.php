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

    /** @var Application $c */
    $c =& $this[CONTAINER_APPLICATION];
    if (!$c instanceof Application) {
        return;
    }

    return new App(
        [
            /**
             * Application Instance
             *
             * Use on closure prevent being binding to
             * @return Application
             */
            CONTAINER_APPLICATION => function () use (&$c) : Application {
                return $c;
            },
            /**
             * Configuration Container
             *
             * Use on closure prevent being binding to
             * @return Config
             */
            CONTAINER_CONFIG => $c->includeScope($c->getContainerDirectory('Config.php'), $this[1]),
            /**
             * Closure
             *
             * @return Database
             */
            CONTAINER_DATABASE => $c->includeScope($c->getContainerDirectory('Database.php')),
            /**
             * Closure
             *
             * @return Environment
             */
            CONTAINER_ENVIRONMENT => $c->includeScope($c->getContainerDirectory('Environment.php')),
            /**
             * Closure
             *
             * @return Hook
             */
            CONTAINER_HOOK => $c->includeScope($c->getContainerDirectory('Hook.php')),
            /**
             * Module Container
             *
             * Closure
             *
             * @return EmbeddedCollection
             */
            CONTAINER_MODULE => $c->includeScope($c->getContainerDirectory('Module.php')),
            /**
             * Extension Container
             *
             * Closure
             *
             * @return EmbeddedCollection
             */
            CONTAINER_EXTENSION => $c->includeScope($c->getContainerDirectory('Extension.php')),
            /**
             * @return array
             */
            CONTAINER_SETTINGS => $c->includeScope($c->getContainerDirectory('Settings.php')),
            /**
             * Session Container
             *
             * Closure
             *
             * @return Session
             */
            CONTAINER_SESSION => $c->includeScope($c->getContainerDirectory('Session.php')),
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
