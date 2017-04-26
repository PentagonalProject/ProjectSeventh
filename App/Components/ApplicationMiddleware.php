<?php
declare(strict_types=1);

/* ------------------------------------------------------ *\
 |             APPLICATION COMPONENT MIDDLEWARE            |
\* ------------------------------------------------------ */

namespace {

    use PentagonalProject\ProjectSeventh\Application;
    use PentagonalProject\ProjectSeventh\Arguments;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Slim\App;
    use Slim\Container;

    if (!isset($this) || ! $this instanceof Arguments) {
        return;
    }

    /** @var Application $c */
    $c =& $this['application'];
    if (!$c instanceof Application) {
        return;
    }

    $slim =& $c->getSlim();
    // middle ware
    $slim->add(function (ServerRequestInterface $request, ResponseInterface $response, App $next) {
        /**
         * @var $this Container
         */
        return $next($request, $response);
    });
}
