<?php
/* ------------------------------------------------------ *\
 |                   MIDDLEWARE EXAMPLE                   |
\* ------------------------------------------------------ */

/**
 * @uses \Slim\App
 * for instance of $this
 */
namespace {

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Slim\App;

    if (!isset($this) || ! $this instanceof App) {
        header('HTTP/1.1 403 Forbidden');
        return;
    }

    // add Middleware
    $this->add(function (ServerRequestInterface $serverRequest, ResponseInterface $response, $next) {
        // do code
        return $next($serverRequest, $response);
    });
}
