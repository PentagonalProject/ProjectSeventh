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
     * @var Container $container
     * @return EmbeddedCollection
     */
    return function (Container $container) : EmbeddedCollection {
        /** @var Config $config */
        $config = $container[CONTAINER_CONFIG];
        $moduleCollection = new EmbeddedCollection(
            $config->get('directory[module]'),
            new ModuleReader($container)
        );

        $moduleCollection->scan();
        $container[CONTAINER_LOG]->debug(
            'Extensions initiated & scan',
            [
                'Count' => $moduleCollection->count()
            ]
        );
        return $moduleCollection;
    };
}
