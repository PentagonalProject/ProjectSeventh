<?php
namespace PentagonalProject\ProjectSeventh\Utilities;

use PentagonalProject\ProjectSeventh\Module;

/**
 * Class ModuleReader
 * @package PentagonalProject\ProjectSeventh\Utilities
 */
class ModuleReader extends EmbeddedReader
{
    /**
     * Base on Abstract Class
     *
     * @var string
     */
    protected $embeddedClass = Module::class;

    /**
     * Base Name of Type
     *
     * @var string
     */
    protected $name = 'Module';
}
