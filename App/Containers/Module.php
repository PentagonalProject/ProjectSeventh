<?php
/* ------------------------------------------------------ *\
 |                    CONTAINER MODULE                    |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'module'
 */
namespace {

    use PentagonalProject\ProjectSeventh\Config;
    use PentagonalProject\ProjectSeventh\Utilities\EmbeddedCollection;
    use PentagonalProject\ProjectSeventh\Utilities\ModuleReader;
    use Slim\Container;

    /**
     * Module Container
     *
     * @var Container $c
     * @return EmbeddedCollection
     */
    return function (Container $c) : EmbeddedCollection {
        /** @var Config $config */
        $config = $c[CONTAINER_CONFIG];
        $moduleCollection = new EmbeddedCollection(
            $config->get('directory[module]'),
            new ModuleReader()
        );
        $moduleCollection->scan();
        return $moduleCollection;
    };
}
