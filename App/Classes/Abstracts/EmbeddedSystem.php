<?php
namespace PentagonalProject\ProjectSeventh\Abstracts;

use PentagonalProject\ProjectSeventh\Interfaces\EmbeddedInterface;

/**
 * Class EmbeddedSystem
 * @package PentagonalProject\ProjectSeventh\Abstracts
 */
abstract class EmbeddedSystem implements EmbeddedInterface
{
    const NAME        = 'name';
    const URI         = 'uri';
    const VERSION     = 'version';
    const AUTHOR      = 'author';
    const AUTHOR_URI  = 'author_uri';
    const DESCRIPTION = 'description';
    const CLASS_NAME  = 'class_name';
    const FILE_PATH   = 'file_path';

    /**
     * EmbeddedSystem Name
     *
     * @var string
     */
    protected $embedded_name = '';

    /**
     * EmbeddedSystem URL
     *
     * @var string
     */
    protected $embedded_uri = '';

    /**
     * EmbeddedSystem Version
     *
     * @var mixed
     */
    protected $embedded_version = '';

    /**
     * EmbeddedSystem Author Name
     *
     * @var string
     */
    protected $embedded_author = '';

    /**
     * EmbeddedSystem Author URL
     *
     * @var string
     */
    protected $embedded_author_uri = '';

    /**
     * EmbeddedSystem Description
     *
     * @var string
     */
    protected $embedded_description = '';

    /**
     * EmbeddedSystem constructor.
     * @final as prevent to instantiate
     */
    final public function __construct()
    {
        $this->getEmbeddedName();
    }

    /**
     * Get EmbeddedSystem Info
     *
     * @return array
     */
    public function getEmbeddedInfo() : array
    {
        return [
            EmbeddedSystem::NAME    => $this->getEmbeddedName(),
            EmbeddedSystem::VERSION => $this->getEmbeddedVersion(),
            EmbeddedSystem::URI     => $this->getEmbeddedUri(),
            EmbeddedSystem::AUTHOR  => $this->getEmbeddedAuthor(),
            EmbeddedSystem::AUTHOR_URI  => $this->getEmbeddedAuthorUri(),
            EmbeddedSystem::DESCRIPTION => $this->getEmbeddedDescription(),
            EmbeddedSystem::CLASS_NAME => get_class($this),
            EmbeddedSystem::FILE_PATH  => $this->getEmbeddedRealPath(),
        ];
    }

    /**
     * Get Reflection
     *
     * @return \ReflectionClass
     */
    final protected function getEmbeddedReflection() : \ReflectionClass
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
    final public function getEmbeddedRealPath() : string
    {
        return $this->getEmbeddedReflection()->getFileName();
    }

    /**
     * Get Name Space
     *
     * @return string
     */
    final public function getEmbeddedNameSpace() : string
    {
        return $this->getEmbeddedReflection()->getNamespaceName();
    }

    /**
     * Get ShortName of Class
     *
     * @return string
     */
    final public function getEmbeddedShortName() : string
    {
        return $this->getEmbeddedReflection()->getShortName();
    }

    /**
     * {@inheritdoc}
     */
    public function getEmbeddedName() : string
    {
        if (!is_string($this->embedded_name)
            || trim($this->embedded_name) == ''
        ) {
            $this->embedded_name = $this->getEmbeddedReflection()->getName();
        }

        return (string) $this->embedded_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmbeddedAuthor() : string
    {
        return (string) $this->embedded_author;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmbeddedVersion() : string
    {
        return (string) $this->embedded_version;
    }

    /**
     * Get EmbeddedSystem URL
     *
     * @return string
     */
    public function getEmbeddedUri(): string
    {
        return (string) $this->embedded_uri;
    }

    /**
     * Get EmbeddedSystem Author
     *
     * @return string
     */
    public function getEmbeddedAuthorUri(): string
    {
        return (string) $this->embedded_author_uri;
    }

    /**
     * Get Description of EmbeddedSystem
     *
     * @return string
     */
    public function getEmbeddedDescription(): string
    {
        return (string) $this->embedded_description;
    }
}
