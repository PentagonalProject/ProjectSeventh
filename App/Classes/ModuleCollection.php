<?php
namespace PentagonalProject\ProjectSeventh;

use Apatis\Exceptions\RuntimeException;

/**
 * Class ModuleCollection
 * @package PentagonalProject\ProjectSeventh
 */
class ModuleCollection
{
    /**
     * @var string
     */
    protected $module_directory;

    /**
     * @var Module[]
     */
    protected $modules = [];

    /**
     * @var array
     */
    protected $activeModules = [];

    /**
     * @var array
     */
    protected $invalidModules = [];

    /**
     * ModuleCollection constructor.
     * @param string $moduleDirectory
     */
    public function __construct(string $moduleDirectory)
    {
        if (!is_dir($moduleDirectory) || is_readable($moduleDirectory)) {
            throw new RuntimeException(
               "Invalid Module Directory. Module directory not exists or has not readable by system!",
                E_COMPILE_ERROR
            );
        }

        $this->module_directory = realpath($moduleDirectory)?: $moduleDirectory;
        clearstatcache($this->module_directory);
    }

    /**
     * Get Path Module Directory
     *
     * @return string
     */
    public function getModuleDirectory(): string
    {
        return $this->module_directory;
    }

    /**
     * @return Module[]
     */
    public function getAllModules()
    {
        return $this->modules;
    }

    public function scan()
    {

    }
}