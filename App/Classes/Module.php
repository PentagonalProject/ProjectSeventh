<?php
namespace PentagonalProject\ProjectSeventh;

use PentagonalProject\ProjectSeventh\Interfaces\ModuleInterface;

/**
 * Class Module
 * @package PentagonalProject\ProjectSeventh
 */
abstract class Module implements ModuleInterface
{
    /**
     * Module Name
     *
     * @var string
     */
    protected $module_name = '';

    /**
     * Module URL
     *
     * @var string
     */
    protected $module_uri = '';

    /**
     * Module Version
     *
     * @var mixed
     */
    protected $module_version = '';

    /**
     * Module Author Name
     *
     * @var string
     */
    protected $module_author = '';

    /**
     * Module Author URL
     *
     * @var string
     */
    protected $module_author_uri = '';

    /**
     * Module Description
     *
     * @var string
     */
    protected $module_description = '';

    /**
     * Module constructor.
     * @final as prevent to instantiate
     */
    final public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleName() : string
    {
        if (!is_string($this->module_name)
            || trim($this->module_name) == ''
        ) {
            $this->module_name = get_class($this);
        }

        return (string) $this->module_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleAuthor() : string
    {
        return (string) $this->module_author;
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleVersion() : string
    {
        return (string) $this->module_version;
    }

    /**
     * Get Module URL
     *
     * @return string
     */
    public function getModuleUri(): string
    {
        return (string) $this->module_uri;
    }

    /**
     * Get Module Author
     *
     * @return string
     */
    public function getModuleAuthorUri(): string
    {
        return (string) $this->module_author_uri;
    }

    /**
     * @return string
     */
    public function getModuleDescription(): string
    {
        return (string) $this->module_description;
    }
}
