<?php
/* ------------------------------------------------------ *\
 |             APPLICATION COMPONENT CONTAINER            |
\* ------------------------------------------------------ */

/**
 * Determine Registered Container
 * On Here
 */
namespace {

    use PentagonalProject\ProjectSeventh\Application;
    use PentagonalProject\ProjectSeventh\Arguments;
    use PentagonalProject\ProjectSeventh\Config;
    use PentagonalProject\ProjectSeventh\Hook;

    if (!isset($this) || ! $this instanceof Arguments) {
        return;
    }

    /** @var Application $app */
    $app =& $this[CONTAINER_APPLICATION];
    if (!$app instanceof Application) {
        return;
    }

    /**
     * Especially Add Shutdown Handler on Application
     */
    register_shutdown_function(function () use ($app) {
        /**
         * @var Hook $hook
         */
        $hook = $app[CONTAINER_HOOK];
        $handler = $hook->apply(
            HOOK_HANDLER_SHUTDOWN,
            $app[CONTAINER_SHUTDOWN]
        );
        if (is_callable($handler)) {
            call_user_func($handler);
        }
    });

    /**
     * Includes Container auto load from config
     *
     * @var Config $config
     */
    $config = $app[CONTAINER_CONFIG];
    if (($Containers = $config['autoload[container]']) && is_array($Containers)) {
        $c = 0;
        // anonymous function to require file as binding Slim\App
        $slimInit = function ($file) {
            /** @noinspection PhpIncludeInspection */
            require_once $file;
        };
        // binding to Slim\App for $this
        $slimInit = $slimInit->bindTo($app->getSlim());
        foreach ($Containers as $Container) {
            if (is_string($Container) && file_exists($Container)) {
                // include
                $slimInit($Container);
                $c++;
            }
        }

        ($c > 0) &&
        $app[CONTAINER_LOG]->debug('Additional Container initiated', [
            'Count' => $c
        ]);
    }
}
