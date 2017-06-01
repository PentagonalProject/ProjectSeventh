<?php
namespace PentagonalProject\ProjectSeventh\Interfaces;

use Psr\Container\ContainerInterface;

/**
 * Interface BaseRouteInterface
 * @package PentagonalProject\ProjectSeventh\Interfaces
 */
interface BaseRouteInterface
{
    public function __construct(ContainerInterface $container);
}
