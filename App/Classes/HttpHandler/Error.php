<?php
namespace PentagonalProject\ProjectSeventh\HttpHandler;

use Monolog\Logger;
use PentagonalProject\ProjectSeventh\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Error
 * @package PentagonalProject\ProjectSeventh\HttpHandler
 */
class Error extends \Slim\Handlers\Error
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * Constructor
     *
     * @param bool $displayErrorDetails Set to true to display full details
     * @param Application $app
     */
    public function __construct($displayErrorDetails = false, Application $app)
    {
        $this->app = $app;
        parent::__construct($displayErrorDetails);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        \Exception $exception
    ) {
        /** @var Logger $log */
        $log = $this->app[CONTAINER_LOG];
        $log->error(
            $exception->getMessage(),
            [
                'file' => $exception->getFile(),
                'code' => $exception->getCode(),
                'line' => $exception->getLine()
            ]
        );

        return parent::__invoke($request, $response, $exception);
    }
}
