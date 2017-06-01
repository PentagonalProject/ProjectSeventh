<?php
namespace PentagonalProject\ProjectSeventh\Abstracts;

use PentagonalProject\ProjectSeventh\Interfaces\BaseRouteInterface;
use Psr\Container\ContainerInterface;

/**
 * Class BaseRouteGroup
 * @package PentagonalProject\ProjectSeventh\Abstracts
 */
abstract class BaseRouteGroup implements BaseRouteInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * BaseRouteGroup constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Invoker For Route Group
     *
     * @return mixed
     */
    abstract public function __invoke();
}
