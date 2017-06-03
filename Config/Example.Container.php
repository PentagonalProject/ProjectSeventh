<?php
/* ------------------------------------------------------ *\
 |                   CONTAINER EXAMPLE                    |
\* ------------------------------------------------------ */

/**
 * @uses \Slim\App
 * for instance of $this
 */
namespace {

    use Psr\Container\ContainerInterface;
    use Slim\App;

    if (!isset($this) || !$this instanceof App) {
        return;
    }
    /**
     * @var ContainerInterface $container
     */
    $container = $this->getContainer();

    /**
     * Example Container
     *
     * create example container
     * @param ContainerInterface $container
     * @return stdClass
     */
    $container['example'] = function (ContainerInterface $container) {
        return new \stdClass();
    };
}
