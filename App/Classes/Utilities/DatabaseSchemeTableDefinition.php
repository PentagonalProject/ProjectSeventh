<?php
namespace PentagonalProject\ProjectSeventh\Utilities;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaConfig;
use Doctrine\DBAL\Schema\Sequence;
use Doctrine\DBAL\Schema\Table;
use PentagonalProject\ProjectSeventh\Database;

/**
 * Class DatabaseSchemeTableDefinition
 * @package PentagonalProject\ProjectSeventh\Utilities
 *
 * example :
 * $scheme = [
 *      "user" => [
 *          "id" => [
 *              // Type Map
 *               "bigint",
 *              // Options @see \Doctrine\DBAL\Schema\Column
 *               [
 *                   "autoincrement" => true,
 *                   "length" => 10,
 *                   "index" => true,
 *               ]
 *           ],
 *           "username" => [
 *               "string",
 *               []
 *           ],
 *       ]
 *   ];
 */
class DatabaseSchemeTableDefinition
{
    const NAME_TYPE = 'type';
    const NAME_OPTIONS = 'options';
    const NAME_PROPERTIES = 'properties';

    /**
     * @var array
     */
    protected $originalScheme;

    /**
     * @var array
     */
    protected $schemes = [];

    /**
     * @var array
     */
    protected $invalidScheme = [];

    /**
     * @var bool
     */
    protected $hasBuild = false;

    /**
     * @var Table[]
     */
    protected $cachedTable;

    /**
     * DatabaseSchemeUtilities constructor.
     * @param array $schemes
     */
    public function __construct(array $schemes)
    {
        $this->originalScheme = $schemes;
    }

    /**
     * Build Scheme
     */
    protected function protectedBuildSchemes()
    {
        if ($this->hasBuild) {
            return;
        }

        $this->hasBuild = true;
        /**
         * List Available Properties
         *
         * @var array
         */
        $availableOptions = [
            'column' => [
                'autoincrement' => 'boolean',
                'length' => 'integer',
                'precision' => 'integer',
                'scale' => 'integer',
                'unsigned' => 'boolean',
                'fixed' => 'boolean',
                'notnull' => 'boolean',
                'default' => 'mixed',
                'comment' => 'string',
            ],
            'table_set' => [
                'primarykey' => 'mixed',
            ],
            'table_add' => [
                'index' => 'mixed',
                'uniqueindex' => 'mixed',
                'foreignkey' => 'array',
                'foreignkeyconstraint' => 'array',
            ],
        ];
        foreach ($this->originalScheme as $table => $definitions) {
            if (!is_array($definitions)) {
                $this->invalidScheme[$table] = true;
                continue;
            }

            foreach ($definitions as $column => $definition) {
                if (isset($this->invalidScheme[$table])) {
                    break;
                }

                $columnType = $this->resolveTypeMap(reset($definition));
                if (!$columnType) {
                    $this->invalidScheme[$table] = true;
                    break;
                }

                $options = (array) next($definition);
                $properties = [];
                foreach ($options as $optionKey => $value) {
                    unset($options[$optionKey]);
                    if (is_numeric($optionKey)) {
                        continue;
                    }
                    $optionKey = strtolower($optionKey);
                    if (isset($availableOptions['column'][$optionKey])) {
                        $type = $availableOptions['column'][$optionKey];
                        if ($type !== 'mixed') {
                            $oldValue = $value;
                            if (!settype($value, $type)) {
                                $value = $oldValue;
                            }
                        }

                        $options[$optionKey] = $value;
                        continue;
                    }
                    if (isset($availableOptions['table_set'][$optionKey])) {
                        if (!is_string($value)) {
                            $value = (bool) $value;
                        }
                        if ($value === false) {
                            continue;
                        }
                        $properties['set'][$optionKey][$column] = $value === '' ? true : $value;
                    } elseif (isset($availableOptions['table_add'][$optionKey])) {
                        $type = $availableOptions['table_add'][$optionKey];
                        if ($type === 'array') {
                            if (gettype($value) !== 'array') {
                                continue;
                            }
                            if (!empty($value)) {
                                $properties['add'][$optionKey][$column] = $value;
                            }
                            continue;
                        }
                        if (!is_string($value)) {
                            $value = (bool) $value;
                        }
                        if ($value) {
                            $properties['add'][$optionKey][$column] = $value;
                        }
                    }
                }

                if (isset($options['autoincrement']) && ! isset($properties['set']['primarykey'][$column])) {
                    $properties['set']['primarykey'][$column] = true;
                }

                $definitions[$column] = [
                    static::NAME_TYPE => $columnType,
                    static::NAME_OPTIONS => $options,
                    static::NAME_PROPERTIES => $properties
                ];
            }

            if (!isset($this->invalidScheme[$table])) {
                $this->schemes[$table] = $definitions;
            }
        }
    }

    /**
     * Get Build Schemes
     *
     * @return array
     */
    public function getSchemes()
    {
        $this->protectedBuildSchemes();

        return $this->schemes;
    }

    /**
     * Get Invalid Scheme
     *
     * @return array
     */
    public function getInvalidSchemes()
    {
        $this->protectedBuildSchemes();
        return $this->invalidScheme;
    }

    /**
     * Convert Into Column
     *
     * @return array|Table[]
     */
    public function getTablesFromSchemes()
    {
        if (!isset($this->cachedTable)) {
            $this->cachedTable = [];
            foreach ($this->getSchemes() as $tableName => $columns) {
                $table = new Table(
                    $tableName
                );
                foreach ($columns as $columnName => $definitions) {
                    $table->addColumn(
                        $columnName,
                        $definitions[static::NAME_TYPE],
                        $definitions[static::NAME_OPTIONS]
                    );
                    if (isset($definitions[static::NAME_PROPERTIES]['set'])) {
                        foreach ($definitions[static::NAME_PROPERTIES]['set'] as $context => $value) {
                            foreach ($value as $newContext => $realValue) {
                                $args = [ [$newContext], (is_string($realValue) ? $realValue : false)];
                                call_user_func_array(
                                    [
                                        $table,
                                        "set{$context}"
                                    ],
                                    $args
                                );
                            }
                        }
                    }
                    if (($definitions[static::NAME_PROPERTIES]['add'])) {
                        foreach ($definitions[static::NAME_PROPERTIES]['add'] as $context => $value) {
                            foreach ($value as $newContext => $realValue) {
                                // use for Foreign Key
                                if (is_array($realValue)) {
                                    $args = [$newContext, $realValue];
                                    $context = 'foreignkeyconstraint';
                                } else {
                                    $args = [ [$newContext], (is_string($realValue) ? $realValue : false)];
                                }
                                call_user_func_array(
                                    [
                                        $table,
                                        "add{$context}"
                                    ],
                                    $args
                                );
                            }
                        }
                    }
                }

                $this->cachedTable[$tableName] = $table;
            }
        }

        return $this->cachedTable;
    }

    /**
     * As Table Use Database
     *
     * @param Database $database
     * @return array|Table[]
     */
    public function getTablesFromSchemesWith(Database $database) : array
    {
        $tables = $this->getTablesFromSchemes();
        foreach ($tables as $tableName => $table) {
            $tables[$tableName] = new Table(
                $database->prefixTables($table->getName()),
                $table->getColumns(),
                $table->getIndexes(),
                $table->getForeignKeys(),
                0,
                $table->getOptions()
            );
        }

        return $tables;
    }

    /**
     * Get Scheme With Given Params
     *
     * @return Schema
     */
    public function getSchemaTablesWith() : Schema
    {
        $database = null;
        $schemaConfig = new SchemaConfig();
        $sequences = [];
        $nameSpace = [];
        foreach (func_get_args() as $key => $value) {
            if ($value instanceof Database) {
                $database = $value;
                continue;
            }
            if ($value instanceof SchemaConfig) {
                $schemaConfig = $value;
                continue;
            }
            if (is_array($value) && count($value) > 0) {
                $isSequenceValid = true;
                $isNameSpaceValid = true;
                foreach ($value as $seq) {
                    if (is_object($value) && ! $seq instanceof Sequence) {
                        $isSequenceValid = false;
                        if (!$isNameSpaceValid) {
                            break;
                        }
                        continue;
                    }

                    if (!is_string($seq)) {
                        $isNameSpaceValid = false;
                        if (!$isSequenceValid) {
                            break;
                        }
                    }
                }

                $sequences = $isSequenceValid ? $value : $sequences;
                $nameSpace = $isNameSpaceValid ? $value : $nameSpace;
            }
        }
        $tables = $database
            ? $this->getTablesFromSchemesWith($database)
            : $this->getTablesFromSchemes();
        return new Schema(
            $tables,
            $sequences,
            $schemaConfig,
            $nameSpace
        );
    }

    /**
     * Get SQL Migration Of Query as Array
     *
     * @param Database $database
     *
     * @return array|string[]
     */
    public function getSQLWithDatabase(Database $database) : array
    {
        /**
         * @var Schema $schema
         */
        $schema = call_user_func_array(
            [$this, 'getSchemaTablesWith'],
            func_get_args()
        );

        /**
         * @var Database $database
         */
        return $schema->getMigrateFromSql(
            $database->getSchemaManager()->createSchema(),
            $database->getDatabasePlatform()
        );
    }

    /**
     * Get Array QUERY SQL From Create Table
     *
     * @param Database $database
     * @return array
     */
    public function getSQLCreateTableWithDatabase(Database $database) : array
    {
        /**
         * @var Schema $schema
         */
        $schema = call_user_func_array(
            [$this, 'getSchemaTablesWith'],
            func_get_args()
        );

        return $schema->getMigrateFromSql(
            new Schema(),
            $database->getDatabasePlatform()
        );
    }

    /**
     * Execute Query From Given Tables
     *
     * @param Database $database
     * @return int
     * @throws \Exception
     */
    public function executeSQLWith(Database $database) : int
    {
        /**
         * array
         */
        $migration = call_user_func_array(
            [$this, 'getSQLWithDatabase'],
            func_get_args()
        );

        $count = 0;
        if (!empty($migration)) {
            $database->beginTransaction();
            try {
                foreach ($migration as $query) {
                    $stmt = $database->prepare($query);
                    $stmt->execute();
                    $stmt->closeCursor();
                    $count++;
                }
                $database->rollBack();
            } catch (\Exception $exception) {
                $database->rollBack();
                throw $exception;
            }
        }

        return $count;
    }

    /**
     * Additional Mapping Type Fix
     *
     * @param string $type
     * @return string|null  null if not match
     */
    public static function resolveTypeMap($type)
    {
        if (!is_string($type)) {
            return null;
        }

        $type = strtolower($type);
        preg_match(
            '#
            (?P<binary>bin)
            | (?P<datetimez>datetimez|timez)
            | (?P<guid>gu|id)
            | (?P<datetime>date)
            | (?P<time>time?)
            | (?P<blob>blob)
            | (?P<text>text|long)
            | (?P<string>enum|char|string|var)
            | (?P<boolean>bool)
            | (?P<smallint>small)
            | (?P<bigint>big)
            | (?P<decimal>dec)
            | (?P<float>float)
            | (?P<integer>int|num(?:ber)?)
            | (?P<json_array>json)
            | (?P<array>array)
            | (?P<object>obj)
        #x',
            $type,
            $match,
            PREG_NO_ERROR
        );

        $match = array_filter($match, function ($value, $key) {
            return ! is_numeric($key) && ! empty($value);
        }, ARRAY_FILTER_USE_BOTH);

        return key($match);
    }
}
