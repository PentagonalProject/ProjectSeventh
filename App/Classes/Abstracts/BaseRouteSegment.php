<?php
namespace PentagonalProject\ProjectSeventh\Abstracts;

use PentagonalProject\ProjectSeventh\Interfaces\BaseRouteInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Class BaseRouteSegment
 * @package PentagonalProject\ProjectSeventh\Abstracts
 */
abstract class BaseRouteSegment implements BaseRouteInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * BaseRouteSegment constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Base Invoker
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $params  slim routes parameters
     * @return ResponseInterface
     */
    abstract public function __invoke(
        RequestInterface $request,
        ResponseInterface $response,
        array $params = []
    ) : ResponseInterface;
}
