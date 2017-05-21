<?php
/* ------------------------------------------------------ *\
 |             APPLICATION COMPONENT ROUTES              |
\* ------------------------------------------------------ */

namespace {

    use PentagonalProject\ProjectSeventh\Application;
    use PentagonalProject\ProjectSeventh\Arguments;
    use Slim\App;

    if (!isset($this) || ! $this instanceof Arguments) {
        return;
    }

    /** @var Application $app */
    $app =& $this[CONTAINER_APPLICATION];
    if (!$app instanceof Application) {
        return;
    }

    /**
     * @var App $slim
     */
     $slim =& $app->getSlim();
     $config = $slim->getContainer()[CONTAINER_CONFIG];
     if (($routes = $config['autoload']['routes'])&& is_array($routes)) {
         foreach ($routes as $route) {
             if (is_string($route) && file_exists($route)) {
                 $app->includeScope($route);
             }
         }
     }
}
