<?php
/* ------------------------------------------------------ *\
 |             APPLICATION COMPONENT MIDDLEWARE            |
\* ------------------------------------------------------ */

namespace {

    use PentagonalProject\ProjectSeventh\Application;
    use PentagonalProject\ProjectSeventh\Arguments;
    use PentagonalProject\ProjectSeventh\Config;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Slim\Http\Uri;

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
     * Includes Middleware auto load from config
     *
     * @var Config $config
     */
    $config = $app[CONTAINER_CONFIG];
    if (($middleWares = $config['autoload[middleware]']) && is_array($middleWares)) {
        $c = 0;
        // anonymous function to require file as binding Slim\App
        $slimInit = function ($file) {
            /** @noinspection PhpIncludeInspection */
            require_once $file;
        };
        // binding to Slim\App for $this
        $slimInit = $slimInit->bindTo($slim);
        foreach ($middleWares as $middleWare) {
            if (is_string($middleWare) && file_exists($middleWare)) {
                $slimInit($middleWare);
                $c++;
            }
        }

        ($c > 0) &&
            $app[CONTAINER_LOG]->debug('Additional Middleware initiated', [
                'Count' => $c
            ]);
    }
}
