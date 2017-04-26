<?php
/* ------------------------------------------------------ *\
 |                   CONTAINER DATABASE                   |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'database'
 * Handle Database abstraction stored object (PDO)
 */
namespace {

    use PentagonalProject\ProjectSeventh\Config;
    use PentagonalProject\ProjectSeventh\Database;
    use \Apatis\Exceptions\InvalidArgumentException;
    use Slim\Container;

    /**
     * Database Container
     *
     * @param Container $c
     * @return Database
     */
    return function (Container $c): Database {
        /**
         * @var Config $config
         */
        $config =& $c['config'];
        $databaseConfig = (array) $config->get('database', []);

        /* ---------------------------------------------------
         *              DATABASE CONFIG VALIDATION
         * ---------------------------------------------------
         */
        $isEmptyDriver = empty($databaseConfig['driver']);
        $isSQLite = (!$isEmptyDriver
            && (
                trim(strtolower($databaseConfig['driver'])) == 'sqlite'
                || trim(strtolower($databaseConfig['driver'])) == 'pdo_sqlite'
            )
        );
        if (empty($databaseConfig['host']) && ! $isSQLite) {
            $databaseConfig['host'] = 'localhost';
        }

        if ($isEmptyDriver) {
            $port = !empty($databaseConfig['port'])
                ? $databaseConfig['port']
                : 3306;
            if (abs($port) === 3306) {
                $databaseConfig['driver'] = 'mysql';
            }
        }

        if (!$isSQLite) {
            if (empty($databaseConfig['user'])) {
                throw new InvalidArgumentException(
                    'Database User could not be empty',
                    E_USER_ERROR
                );
            }

            if (!is_string($databaseConfig['user'])) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Database User must be as a string, %s given.',
                        gettype($databaseConfig['user'])
                    ),
                    E_USER_ERROR
                );
            }

            if (empty($databaseConfig['name'])) {
                throw new InvalidArgumentException(
                    'Database Name could not be empty',
                    E_USER_ERROR
                );
            }

            if (!is_string($databaseConfig['name'])) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Database Name must be as a string, %s given.',
                        gettype($databaseConfig['name'])
                    ),
                    E_USER_ERROR
                );
            }
        } else {
            // if doing mysql
            if (empty($databaseConfig['name']) && empty($databaseConfig['path'])) {
                throw new InvalidArgumentException(
                    'Database Path could not be empty.',
                    E_USER_ERROR
                );
            }
            $path = ! empty($databaseConfig['path']) && is_string($databaseConfig['path'])
                ? $databaseConfig['path']
                : $databaseConfig['name'];
            if (!is_string($path)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Database Path must be as a string, %s given.',
                        gettype($path)
                    ),
                    E_USER_ERROR
                );
            }
        }

        /* ---------------------------------------------------
         *           END DATABASE CONFIG VALIDATION
         * ---------------------------------------------------
         */

        // set new Config
        $config->set('database', $databaseConfig);
        $database = new Database($databaseConfig);
        return $database;
    };
}
