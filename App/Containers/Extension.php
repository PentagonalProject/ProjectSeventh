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
     * @var Container $c
     * @return EmbeddedCollection
     */
    return function (Container $c) : EmbeddedCollection {
        /** @var Config $config */
        $config = $c['config'];
        $extensionCollection = new EmbeddedCollection(
            $config->get('directory[extension]'),
            new ExtensionReader()
        );
        $extensionCollection->scan();
        return $extensionCollection;
    };
}
