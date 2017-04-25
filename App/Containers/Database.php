<?php
/**
 * File for Slim Container for 'database'
 * Handle Database abstraction stored object (PDO)
 */
namespace {

    use PentagonalProject\ProjectSeventh\Config;
    use PentagonalProject\ProjectSeventh\Database;
    use Slim\Container;

    /**
     * Database Container
     *
     * @param Container $c
     * @return Database
     */
    return function ($c): Database {
        /**
         * @var Config $config
         */
        $config = $c['config'];
        return new Database((array) $config->get('database', []));
    };
}
