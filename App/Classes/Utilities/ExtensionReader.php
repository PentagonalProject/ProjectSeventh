<?php
namespace PentagonalProject\ProjectSeventh\Utilities;

use PentagonalProject\ProjectSeventh\Extension;

/**
 * Class ExtensionReader
 * @package PentagonalProject\ProjectSeventh\Utilities
 */
class ExtensionReader extends EmbeddedReader
{
    /**
     * Base on Abstract Class
     *
     * @var string
     */
    protected $embeddedClass = Extension::class;

    /**
     * Base Name of Type
     *
     * @var string
     */
    protected $name = 'Extension';
}
