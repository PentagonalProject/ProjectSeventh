<?php
/* ------------------------------------------------------ *\
 |             APPLICATION COMPONENT ROUTES              |
\* ------------------------------------------------------ */

namespace {

    use PentagonalProject\ProjectSeventh\Application;
    use PentagonalProject\ProjectSeventh\Arguments;
    use PentagonalProject\ProjectSeventh\Config;
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
     * @var Config $config
     */
    $config = $app[CONTAINER_CONFIG];
    if (($routes = $config['autoload[routes]'])&& is_array($routes)) {
        $c = 0;
        foreach ($routes as $route) {
            if (is_string($route) && file_exists($route)) {
                $app->includeScope($route);
                $c++;
            }
        }
        ($c > 0) &&
            $app[CONTAINER_LOG]->debug('Additional Routes initiated', [
                'Count' => $c
            ]);
    }
}
