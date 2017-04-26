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
    $c =& $this['application'];
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
            'application' => function () use (&$c) : Application {
                return $c;
            },
            /**
             * Configuration Container
             *
             * Use on closure prevent being binding to
             * @return Config
             */
            'config' => $c->includeScope($c->getContainerDirectory('Config.php'), $this[1]),
            /**
             * Closure
             *
             * @return Database
             */
            'database'    => $c->includeScope($c->getContainerDirectory('Database.php')),
            /**
             * Closure
             *
             * @return Environment
             */
            'environment' => $c->includeScope($c->getContainerDirectory('Environment.php')),
            /**
             * Closure
             *
             * @return Hook
             */
            'hook' => $c->includeScope($c->getContainerDirectory('Hook.php')),
            /**
             * Module Container
             *
             * Closure
             *
             * @return EmbeddedCollection
             */
            'module'      => $c->includeScope($c->getContainerDirectory('Module.php')),
            /**
             * Extension Container
             *
             * Closure
             *
             * @return EmbeddedCollection
             */
            'extension'    => $c->includeScope($c->getContainerDirectory('Extension.php')),
            /**
             * @return array
             */
            'settings'    => $c->includeScope($c->getContainerDirectory('Settings.php')),
            /**
             * Session Container
             *
             * Closure
             *
             * @return Session
             */
            'session'    => $c->includeScope($c->getContainerDirectory('Session.php')),
            /**
             * Slim Inheritance
             *
             * @return App
             */
            'slim'        => function (Container $container) : App {
                /**
                 * @var Application $application
                 */
                $application =& $container['application'];
                return $application->getSlim();
            }
        ]
    );
}
