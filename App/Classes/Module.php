<?php
namespace PentagonalProject\ProjectSeventh;

use PentagonalProject\ProjectSeventh\Interfaces\ModuleInterface;

/**
 * Class Module
 * @package PentagonalProject\ProjectSeventh
 */
abstract class Module implements ModuleInterface
{
    const MODULE_NAME       = 'name';
    const MODULE_URI        = 'uri';
    const MODULE_VERSION    = 'version';
    const MODULE_AUTHOR     = 'author';
    const MODULE_AUTHOR_URI = 'author_uri';
    const MODULE_DESCRIPTION = 'description';

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
        $this->getModuleName();
    }

    /**
     * Get Module Info
     *
     * @return array
     */
    public function getModuleInfo() : array
    {
        return [
            Module::MODULE_NAME    => $this->getModuleName(),
            Module::MODULE_VERSION => $this->getModuleVersion(),
            Module::MODULE_URI     => $this->getModuleUri(),
            Module::MODULE_AUTHOR  => $this->getModuleAuthor(),
            Module::MODULE_AUTHOR_URI  => $this->getModuleAuthorUri(),
            Module::MODULE_DESCRIPTION => $this->getModuleDescription()
        ];
    }

    /**
     * @return \ReflectionClass
     */
    final protected function getReflection() : \ReflectionClass
    {
        static $reflection;
        if (!$reflection || ! $reflection instanceof \ReflectionClass) {
            $reflection = new \ReflectionClass($this);
        }

        return $reflection;
    }

    /**
     * Get Path
     *
     * @return string
     */
    final public function getModuleRealPath() : string
    {
        return $this->getReflection()->getFileName();
    }

    /**
     * Get Name Space
     *
     * @return string
     */
    final public function getModuleNameSpace() : string
    {
        return $this->getReflection()->getNamespaceName();
    }

    /**
     * Get ShortName of Class
     *
     * @return string
     */
    final public function getModuleShortName() : string
    {
        return $this->getReflection()->getShortName();
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleName() : string
    {
        if (!is_string($this->module_name)
            || trim($this->module_name) == ''
        ) {
            $this->module_name = $this->getReflection()->getName();
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
     * Get Description of Module
     *
     * @return string
     */
    public function getModuleDescription(): string
    {
        return (string) $this->module_description;
    }
}
