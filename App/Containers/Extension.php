<?php
/* ------------------------------------------------------ *\
 |                 CONTAINER EXTENSION                    |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'module'
 */
namespace {

    use PentagonalProject\ProjectSeventh\Config;
    use PentagonalProject\ProjectSeventh\Utilities\EmbeddedCollection;
    use PentagonalProject\ProjectSeventh\Utilities\ExtensionReader;
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
        $extensionCollection = new EmbeddedCollection(
            $config->get('directory[extension]'),
            new ExtensionReader()
        );
        $extensionCollection->scan();
        $container[CONTAINER_LOG]->debug(
            'Extensions initiated & scan',
            [
                'Count' => $extensionCollection->count()
            ]
        );
        return $extensionCollection;
    };
}
