<?php
namespace PentagonalProject\ProjectSeventh;

use Apatis\ArrayStorage\Collection;
use Apatis\Exceptions\InvalidArgumentException;
use Apatis\Exceptions\RuntimeException;
use PentagonalProject\ProjectSeventh\Exceptions\InvalidModuleException;
use PentagonalProject\ProjectSeventh\Exceptions\ModuleNotFoundException;
use RecursiveDirectoryIterator;
use SplFileInfo;

/**
 * Class ModuleCollection
 * @package PentagonalProject\ProjectSeventh
 */
class ModuleCollection implements \Countable, \ArrayAccess
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
    protected $moduleDirectory;

    /**
     * @var string[]
     */
    protected $unwantedPath = [];

    /**
     * @var Module[]|\Closure[]
     */
    protected $validModules = [];

    /**
     * @var \Exception[]
     */
    protected $invalidModules = [];

    /**
     * @var string[]
     */
    protected $loadedModules = [];

    /**
     * @var bool
     */
    protected $hasScanned = false;

    /**
     * @var array
     */
    protected $moduleDefaultInfo = [
        Module::MODULE_NAME,
        Module::MODULE_VERSION,
        Module::MODULE_URI,
        Module::MODULE_AUTHOR,
        Module::MODULE_AUTHOR_URI,
        Module::MODULE_DESCRIPTION
    ];

    /**
     * ModuleCollection constructor.
     * @param string $moduleDirectory
     */
    public function __construct(string $moduleDirectory)
    {
        if (!is_dir($moduleDirectory) || ! is_readable($moduleDirectory)) {
            throw new RuntimeException(
               "Invalid Module Directory. Module directory not exists or has not readable by system!",
                E_COMPILE_ERROR
            );
        }

        $this->splFileInfo = new \SplFileInfo($moduleDirectory);
        if ($this->splFileInfo->isLink()) {
            throw new RuntimeException(
                "Invalid Module Directory. Module directory could not as a symlink!",
                E_COMPILE_ERROR
            );
        }

        $this->moduleDirectory = $this->splFileInfo->getRealPath();
    }

    /**
     * Get Path Module Directory
     *
     * @return string
     */
    public function getModuleDirectory(): string
    {
        return $this->moduleDirectory;
    }

    /**
     * @return Module[]
     */
    public function getAllModules() : array
    {
        return $this->validModules;
    }

    /**
     * Scan Module Directory
     *
     * @return ModuleCollection
     */
    public function scan() : ModuleCollection
    {
        if ($this->hasScanned) {
            return $this;
        }

        /**
         * @var SplFileInfo $path
         */
        foreach (new RecursiveDirectoryIterator($this->getModuleDirectory()) as $path) {
            $baseName = $path->getBaseName();
            // skip dotted
            if ($baseName == '.' || $baseName == '..') {
                continue;
            }

            $directory = $this->getModuleDirectory() . DIRECTORY_SEPARATOR . $baseName;
            // don't allow symlink to be execute & skip if contains file
            if ($path->isLink() || ! $path->isDir()) {
                $this->unwantedPath[$baseName] = $path->getType();
                continue;
            }

            $file = $directory . DIRECTORY_SEPARATOR . $baseName .'.php';
            if (! file_exists($file)) {
                $this->invalidModules[$baseName] = new ModuleNotFoundException(
                    $file,
                    sprintf("Module file for %s has not found", $baseName)
                );

                continue;
            }

            try {
                $this->validModules[$this->sanitizeModuleName($baseName)] = function () use ($file) {
                    $module = (new ModuleReader($file))->process();
                    return $module->getInstance();
                };

            } catch (\Exception $e) {
                $this->invalidModules[$this->sanitizeModuleName($baseName)] = $e;
            }
        }

        return $this;
    }

    /**
     * Get Invalid Modules
     *
     * @return \Exception[]
     */
    public function getInvalidModules(): array
    {
        return $this->invalidModules;
    }

    /**
     * Get Unwanted Path
     * contain [file|dir|link]
     *
     * @see ModuleCollection::TYPE_SYMLINK
     * @see ModuleCollection::TYPE_DIR
     * @see ModuleCollection::TYPE_FILE
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
     * Get Loaded Modules List base on Name
     *
     * @return string[]
     */
    public function getLoadedModulesName(): array
    {
        return $this->loadedModules;
    }

    /**
     * @return Module[]
     */
    public function getLoadedModules(): array
    {
        $module = [];
        foreach ($this->getLoadedModulesName() as $value) {
            if (isset($this->validModules[$value])) {
                $module[$value] = $this->validModules[$value];
            }
        }

        return $module;
    }

    /**
     * Sanitize Module Name
     *
     * @param string $name
     * @return string
     */
    protected function sanitizeModuleName(string $name) : string
    {
        return trim(strtolower($name));
    }

    /**
     * Get Module Given By Name
     *
     * @access internal
     * @param string $name
     * @return Module
     * @throws InvalidModuleException
     */
    protected function &internalGetModule(string $name) : Module
    {
        $moduleName = $this->sanitizeModuleName($name);
        if (!$moduleName) {
            throw new InvalidArgumentException(
                "Please insert not an empty arguments",
                E_USER_ERROR
            );
        }
        if (!$this->exist($moduleName)) {
            throw new InvalidModuleException(
                sprintf("Module %s has not found", $name)
            );
        }
        if ($this->validModules[$moduleName] instanceof \Closure) {
            $this->validModules[$moduleName] = $this->validModules[$moduleName]();
        }

        return $this->validModules[$moduleName];
    }

    /**
     * Get Module Info
     *
     * @param string $moduleName
     * @return Collection
     */
    public function getModuleInfo(string $moduleName)
    {
        return new Collection($this->internalGetModule($moduleName)->getModuleInfo());
    }

    /**
     * Load Module
     *
     * @param string $name
     * @return Module
     * @throws InvalidModuleException
     * @throws ModuleNotFoundException
     */
    public function &load(string $name) : Module
    {
        $module =& $this->internalGetModule($name);
        if (!$this->hasLoaded($name)) {
            $module->init();
            $this->loadedModules[$this->sanitizeModuleName($name)] = true;
        }

        return $module;
    }

    /**
     * Check if Module Exists
     *
     * @param string $name
     * @return bool
     */
    public function exist(string $name)
    {
        return isset($this->validModules[$this->sanitizeModuleName($name)]);
    }

    /**
     * Check If Module Has Loaded
     *
     * @param string $name
     * @return bool
     */
    public function hasLoaded(string $name)
    {
        $moduleName = $this->sanitizeModuleName($name);
        return $moduleName && !empty($this->loadedModules[$moduleName]);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->validModules);
    }

    /**
     * @param string $offset
     * @return Module
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
     * @return Module
     */
    public function __get($name)
    {
        return $this->load($name);
    }
}
