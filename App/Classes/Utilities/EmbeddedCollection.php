<?php
namespace PentagonalProject\ProjectSeventh\Utilities;

use Apatis\ArrayStorage\Collection;
use Apatis\Exceptions\InvalidArgumentException;
use Apatis\Exceptions\RuntimeException;
use PentagonalProject\ProjectSeventh\Abstracts\EmbeddedSystem;
use PentagonalProject\ProjectSeventh\Exceptions\InvalidEmbeddedException;
use PentagonalProject\ProjectSeventh\Exceptions\EmbeddedNotFoundException;
use RecursiveDirectoryIterator;
use SplFileInfo;

/**
 * Class EmbeddedCollection
 * @package PentagonalProject\ProjectSeventh\Utilities
 */
class EmbeddedCollection implements \Countable, \ArrayAccess
{
    const TYPE_DIR = 'dir';
    const TYPE_SYMLINK = 'link';
    const TYPE_FILE = 'file';

    /**
     * @var RecursiveDirectoryIterator
     */
    protected $splFileInfo;

    /**
     * @var string
     */
    protected $embeddedDirectory;

    /**
     * @var string[]
     */
    protected $unwantedPath = [];

    /**
     * @var EmbeddedSystem[]|\Closure[]
     */
    protected $validEmbedded = [];

    /**
     * @var \Exception[]
     */
    protected $invalidEmbedded = [];

    /**
     * @var string[]
     */
    protected $loadedEmbedded = [];

    /**
     * @var bool
     */
    protected $hasScanned = false;

    /**
     * @var array
     */
    protected $embeddedDefaultInfo = [
        EmbeddedSystem::NAME,
        EmbeddedSystem::VERSION,
        EmbeddedSystem::URI,
        EmbeddedSystem::AUTHOR,
        EmbeddedSystem::AUTHOR_URI,
        EmbeddedSystem::DESCRIPTION,
        EmbeddedSystem::CLASS_NAME,
        EmbeddedSystem::FILE_PATH
    ];

    /**
     * @var EmbeddedReader
     */
    protected $embeddedReader;

    /**
     * EmbeddedCollection constructor.
     * @param string $embeddedDirectory
     * @param EmbeddedReader $embeddedReader
     */
    public function __construct(string $embeddedDirectory, EmbeddedReader $embeddedReader)
    {
        $this->embeddedReader = $embeddedReader;
        if (!is_dir($embeddedDirectory) || ! is_readable($embeddedDirectory)) {
            throw new RuntimeException(
                sprintf(
                    'Invalid %s Directory. %s directory not exists or has not readable by system!',
                    $this->embeddedReader->getName()
                ),
                E_COMPILE_ERROR
            );
        }

        $this->splFileInfo = new \SplFileInfo($embeddedDirectory);
        if ($this->splFileInfo->isLink()) {
            throw new RuntimeException(
                sprintf(
                    'Invalid %s Directory. %s directory could not as a symlink!',
                    $this->embeddedReader->getName()
                ),
                E_COMPILE_ERROR
            );
        }

        $this->embeddedDirectory = $this->splFileInfo->getRealPath();
    }

    /**
     * Get Path Embed Directory
     *
     * @return string
     */
    public function getEmbeddedDirectory(): string
    {
        return $this->embeddedDirectory;
    }

    /**
     * @return EmbeddedSystem[]
     */
    public function getAllEmbedded() : array
    {
        return $this->validEmbedded;
    }

    /**
     * Scan EmbeddedSystem Directory
     *
     * @return EmbeddedCollection
     */
    public function scan() : EmbeddedCollection
    {
        if ($this->hasScanned) {
            return $this;
        }

        /**
         * @var SplFileInfo $path
         */
        foreach (new RecursiveDirectoryIterator($this->getEmbeddedDirectory()) as $path) {
            $baseName = $path->getBaseName();
            // skip dotted
            if ($baseName == '.' || $baseName == '..') {
                continue;
            }

            $directory = $this->getEmbeddedDirectory() . DIRECTORY_SEPARATOR . $baseName;
            // don't allow symlink to be execute & skip if contains file
            if ($path->isLink() || ! $path->isDir()) {
                $this->unwantedPath[$baseName] = $path->getType();
                continue;
            }

            $file = $directory . DIRECTORY_SEPARATOR . $baseName .'.php';
            if (! file_exists($file)) {
                $this->invalidEmbedded[$baseName] = new EmbeddedNotFoundException(
                    $file,
                    sprintf(
                        '%1$s file for %2$s has not found',
                        $this->embeddedReader->getName(),
                        $baseName
                    )
                );

                continue;
            }

            try {
                $this->validEmbedded[$this->sanitizeEmbeddedName($baseName)] = function () use ($file) {
                    $embedded = $this->embeddedReader->create($file)->process();
                    return $embedded->getInstance();
                };
            } catch (\Exception $e) {
                $this->invalidEmbedded[$this->sanitizeEmbeddedName($baseName)] = $e;
            }
        }

        return $this;
    }

    /**
     * Get Invalid Embedded
     *
     * @return \Exception[]
     */
    public function getInvalidEmbedded(): array
    {
        return $this->invalidEmbedded;
    }

    /**
     * Get Unwanted Path
     * contain [file|dir|link]
     *
     * @see EmbeddedCollection::TYPE_SYMLINK
     * @see EmbeddedCollection::TYPE_DIR
     * @see EmbeddedCollection::TYPE_FILE
     *
     * @return string[]
     */
    public function getUnwantedPath(): array
    {
        return $this->unwantedPath;
    }

    /**
     * @see getUnwantedPath()
     *
     * @return string[]
     */
    public function getUnwantedPaths(): array
    {
        return $this->unwantedPath;
    }

    /**
     * Get Loaded Embedded List base on Name
     *
     * @return string[]
     */
    public function getLoadedEmbeddedName(): array
    {
        return $this->loadedEmbedded;
    }

    /**
     * @return EmbeddedSystem[]
     */
    public function getLoadedEmbedded(): array
    {
        $embeds = [];
        foreach ($this->getLoadedEmbeddedName() as $value) {
            if (isset($this->validEmbedded[$value])) {
                $embeds[$value] = $this->validEmbedded[$value];
            }
        }

        return $embeds;
    }

    /**
     * Sanitize Embedded Name
     *
     * @param string $name
     * @return string
     */
    public function sanitizeEmbeddedName(string $name) : string
    {
        return trim(strtolower($name));
    }

    /**
     * Get Embedded Given By Name
     *
     * @access internal
     * @param string $name
     * @return EmbeddedSystem
     * @throws InvalidEmbeddedException
     */
    protected function &internalGetEmbedded(string $name) : EmbeddedSystem
    {
        $embeddedName = $this->sanitizeEmbeddedName($name);
        if (!$embeddedName) {
            throw new InvalidArgumentException(
                "Please insert not an empty arguments",
                E_USER_ERROR
            );
        }
        if (!$this->exist($embeddedName)) {
            throw new InvalidEmbeddedException(
                sprintf(
                    '%1$s %2$s has not found',
                    $this->embeddedReader->getName(),
                    $name
                )
            );
        }
        if ($this->validEmbedded[$embeddedName] instanceof \Closure) {
            $this->validEmbedded[$embeddedName] = $this->validEmbedded[$embeddedName]();
        }

        return $this->validEmbedded[$embeddedName];
    }

    /**
     * Get EmbeddedSystem's Info
     *
     * @param string $embeddedName
     * @return Collection
     */
    public function getEmbedInfo(string $embeddedName)
    {
        return new Collection($this->internalGetEmbedded($embeddedName)->getEmbeddedInfo());
    }

    /**
     * Get All EmbeddedSystem Info
     *
     * @return Collection|Collection[]
     */
    public function getAllEmbeddedInfo()
    {
        $embed_info = new Collection();
        foreach ($this->getAllEmbedded() as $embedName => $embed) {
            $embed_info[$embedName] = $this->getEmbedInfo($embedName);
        }

        return $embed_info;
    }

    /**
     * Load Embed
     *
     * @param string $name
     * @return EmbeddedSystem
     * @throws InvalidEmbeddedException
     * @throws EmbeddedNotFoundException
     */
    public function &load(string $name) : EmbeddedSystem
    {
        $embedded =& $this->internalGetEmbedded($name);
        if (!$this->hasLoaded($name)) {
            $embedded->init();
            $this->loadedEmbedded[$this->sanitizeEmbeddedName($name)] = true;
        }

        return $embedded;
    }

    /**
     * Check if Embed Exists
     *
     * @param string $name
     * @return bool
     */
    public function exist(string $name)
    {
        return isset($this->validEmbedded[$this->sanitizeEmbeddedName($name)]);
    }

    /**
     * Check If Embed Has Loaded
     *
     * @param string $name
     * @return bool
     */
    public function hasLoaded(string $name)
    {
        $embedName = $this->sanitizeEmbeddedName($name);
        return $embedName && !empty($this->loadedEmbedded[$embedName]);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->validEmbedded);
    }

    /**
     * @param string $offset
     * @return EmbeddedSystem
     */
    public function offsetGet($offset)
    {
        return $this->load($offset);
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->exist($offset);
    }

    /**
     * {@inheritdoc}
     * no affected here
     */
    public function offsetSet($offset, $value)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        return;
    }

    /**
     * @param string $name
     * @return EmbeddedSystem
     */
    public function __get($name)
    {
        return $this->load($name);
    }
}
