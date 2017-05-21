<?php
namespace PentagonalProject\ProjectSeventh\HttpHandler;
use Monolog\Logger;
use PentagonalProject\ProjectSeventh\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class PhpError
 * @package PentagonalProject\ProjectSeventh\HttpHandler
 */
class PhpError extends \Slim\Handlers\PhpError
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
        \Throwable $error
    ) {
        /** @var Logger $log */
        $log = $this->app[CONTAINER_LOG];
        $log->error(
            $error->getMessage(),
            [
                'file' => $error->getFile(),
                'code' => $error->getCode(),
                'line' => $error->getLine()
            ]
        );

        return parent::__invoke($request, $response, $error);
    }
}