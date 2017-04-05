<?php
namespace Pentagonal\ProjectApi;

/**
 * Class AutoLoaderClass
 * @package Pentagonal\ProjectApi
 */
class AutoLoaderClass
{
    const NAME_SPACE_KEY = '__NAMESPACE__';
    const CLASS_NAME_KEY = '__CLASS__';

    /**
     * Reference collections about class
     *
     * @var array
     */
    private static $references = [];

    /**
     * Reference Class Loaded
     *
     * @var array
     */
    private static $classLoadedRef = [];

    /**
     * Base on Directory
     *
     * @var array
     */
    protected $baseDirectory = [];

    /**
     * AutoLoaderClass constructor.
     * @param string       $nameSpace
     * @param string|array $directory
     */
    public function __construct($nameSpace, $directory)
    {
        $this->nameSpace = $this->resolveNameSpace($nameSpace);
        $this->baseDirectory = (array) $directory;
    }

    /**
     * @return array
     */
    public static function getReferences()
    {
        return AutoLoaderClass::$references;
    }

    /**
     * Resolve Name Space
     *
     * @param string $string
     * @return string
     */
    protected function resolveNameSpace($string)
    {
        return preg_replace(
            '`(\\\)+`',
            '\\',
            trim($string, '\\')
        );
    }

    /**
     * Resolve Name Space To Lower
     *
     * @param string $string
     * @return string
     */
    protected function resolveNameSpaceLower($string)
    {
        return $this->toLower($this->resolveNameSpace($string));
    }

    /**
     * To Lower Case
     *
     * @param string $string
     * @return string
     */
    protected function toLower($string)
    {
        // sanity case insensitive class
        return strtolower($string);
    }

    /**
     * Push Into Reference
     *
     * @param string $group the group / Name Space
     * @param string $class the class Name
     * @param string $file  the absolute file
     * @return bool|string
     */
    private function pushReference($group, $class, $file)
    {
        if ($file = stream_resolve_include_path($file)) {
            $ref =& AutoLoaderClass::$references;
            // references name space
            $nameSpace = $this->resolveNameSpace($group);
            $group     = $this->toLower($nameSpace);
            if ($this->hasGroupReference($group)) {
                $ref[$group] = [];
            }

            return $ref[$group][$this->resolveNameSpaceLower($class)] = $file;
        }

        return false;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function load($class)
    {
        if (!is_string($class)) {
            return false;
        }

        if ($file = $this->findFileFor($class)) {
            // prevent multiple call instance of class
            if (class_exists($class)) {
                return true;
            }

            IncludeFileOnce($file);
            return true;
        }

        return false;
    }

    /**
     * Create Instance
     *
     * @param string       $nameSpace
     * @param string|array $directory
     * @return static
     */
    public static function create($nameSpace, $directory)
    {
        return new static($nameSpace, $directory);
    }

    /**
     * Create Object & Register
     *
     * @param string       $nameSpace the name space
     * @param string|array $directory directory to scan
     * @return bool
     */
    public static function createRegister($nameSpace, $directory)
    {
        return (new static($nameSpace, $directory))->register();
    }

    /**
     * Register Auto load
     *
     * @param bool $prepend
     * @return bool
     */
    public function register($prepend = false)
    {
        return spl_autoload_register([$this, 'load'], true, $prepend);
    }

    /**
     * Un-Registers this instance as an autoloader.
     */
    public function unRegister()
    {
        spl_autoload_unregister([$this, 'load']);
    }

    /**
     * Get File For Class
     *
     * @param string $class
     * @return bool|string
     */
    protected function findFileFor($class)
    {
        $class = $this->resolveNameSpaceLower($class);
        if (isset(self::$classLoadedRef[$class])) {
            return self::$classLoadedRef[$class];
        }

        if (false !== $file = $this->getClassReference($class)) {
            return $file;
        }

        foreach ($this->baseDirectory as $directory) {
            if (!is_string($directory) || ! ($directory = realpath($directory))) {
                continue;
            }

            if (false !== $pos = strpos($class, $this->nameSpace)) {
                $class = substr($class, $pos+1);
            }

            $file = $this->pushReference(
                $this->nameSpace,
                $class,
                $directory . DIRECTORY_SEPARATOR . $class . '.php'
            );

            if ($file) {
                self::$classLoadedRef[$this->resolveNameSpaceLower($class)] = $file;
                break;
            }
        }

        return $file;
    }

    /**
     * Split Name Space Key
     *
     * @param string $class
     * @return array
     */
    protected function splitClassNameSpace($class)
    {
        $class     = $this->resolveNameSpace($class);
        $nameSpaceArray = explode('\\', $class);
        $nameSpace = '';
        $class = end($nameSpaceArray);
        array_shift($nameSpaceArray);
        if (count($nameSpaceArray) > 1) {
            $nameSpace = implode('\\', $nameSpaceArray);
        }
        return [
            self::NAME_SPACE_KEY => $nameSpace,
            self::CLASS_NAME_KEY => $class,
        ];
    }

    /**
     * Get Reference
     * @param string $class
     * @return bool|array
     */
    protected function getClassReference($class)
    {
        // check if not as a class
        if (!$class || substr($class, -1) == '\\') {
            return false;
        }

        $splitClass = $this->splitClassNameSpace($class);
        $group      = $this->getGroupReference($splitClass[self::NAME_SPACE_KEY]);

        return $group && isset($group[$splitClass[self::CLASS_NAME_KEY]])
            ? $group[$splitClass[self::CLASS_NAME_KEY]]
            : false;
    }

    /**
     * Getting References
     *
     * @param string $group
     * @return bool|mixed
     */
    protected function getGroupReference($group)
    {
        if ($this->hasGroupReference($group)) {
            return AutoLoaderClass::$references[$this->resolveNameSpace($group)];
        }

        return false;
    }

    /**
     * Has Group reference
     *
     * @param string $group
     * @return bool
     */
    protected function hasGroupReference($group)
    {
        $group = $this->resolveNameSpaceLower($group);
        return isset(AutoLoaderClass::$references[$group]);
    }
}

/**
 * @param string $file
 */
function IncludeFileOnce($file)
{
    /** @noinspection PhpIncludeInspection */
    include_once $file;
}
