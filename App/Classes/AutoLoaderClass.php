<?php
namespace PentagonalProject\ProjectSeventh;

/**
 * Class AutoLoaderClass
 * @package PentagonalProject\ProjectSeventh
 *
 * PSR-4 Auto Loader
 */
final class AutoLoaderClass
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
    private static $classMapReference = [];

    /**
     * @var array
     */
    protected $classMap = [];

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
    public function __construct(string $nameSpace, string $directory)
    {
        $this->nameSpace = $this->resolveNameSpace($nameSpace);
        $this->baseDirectory = (array) $directory;
    }

    /**
     * @return array
     */
    public static function getReferences() : array
    {
        return self::$references;
    }

    /**
     * @return string[] class map with lower case key
     */
    public function getClassMap() : array
    {
        return $this->classMap;
    }

    /**
     * Resolve Name Space
     *
     * @param string $string
     * @return string
     */
    protected function resolveNameSpace(string $string) : string
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
    protected function resolveNameSpaceLower($string) : string
    {
        return $this->toLower($this->resolveNameSpace($string));
    }

    /**
     * To Lower Case
     *
     * @param string $string
     * @return string
     */
    protected function toLower($string) : string
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
    private function pushReference(string $group, string $class, string $file)
    {
        if ($file = stream_resolve_include_path($file)) {
            // references name space
            $nameSpace = $this->resolveNameSpace($group);
            $group     = $this->toLower($nameSpace);
            if ($this->hasGroupReference($group)) {
                self::$references[$group] = [];
            }
            $class = $this->resolveNameSpaceLower($class);
            $this->classMap[$class] = $file;
            return self::$references[$group][$class] = $file;
        }

        return false;
    }

    /**
     * Load Class
     *
     * @param string $class
     * @return bool
     */
    public function load(string $class) : bool
    {
        if (!is_string($class)) {
            return false;
        }
        if ($file = $this->findFileFor($class)) {
            // prevent multiple call of class
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
     * @return AutoLoaderClass
     */
    public static function create(string $nameSpace, string $directory) : AutoLoaderClass
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
    public static function createRegister(string $nameSpace, string $directory) : bool
    {
        return (new static($nameSpace, $directory))->register();
    }

    /**
     * @param array $nameSpaceAndDirectory
     */
    public static function createRegisterArray(array $nameSpaceAndDirectory)
    {
        foreach ($nameSpaceAndDirectory as $key => $item) {
            self::createRegister($key, $item);
        }
    }

    /**
     * Register Auto load
     *
     * @param bool $prepend
     * @return bool
     */
    public function register(bool $prepend = false) : bool
    {
        return spl_autoload_register($this, true, $prepend);
    }

    /**
     * Un-Registers this instance as an auto loader.
     */
    public function unRegister()
    {
        spl_autoload_unregister($this);
    }

    /**
     * Get File For Class
     *
     * @param string $Class
     * @return bool|string
     */
    protected function findFileFor(string $Class)
    {
        $Class = $this->resolveNameSpace($Class);
        $class = $this->toLower($Class);
        if ($class) {
            if (isset($this->classMap[$class])) {
                return $this->classMap[$class];
            }
            if (isset(self::$classMapReference[$class])) {
                return self::$classMapReference[$class];
            }
        }

        if (false !== $file = $this->getClassReference($class)) {
            return $file;
        }

        $file = false;
        $namespace= $this->resolveNameSpaceLower($this->nameSpace);
        foreach ($this->baseDirectory as $directory) {
            if (!is_string($directory) || ! ($directory = realpath($directory))) {
                continue;
            }

            if (0 === $pos = strpos($class, $namespace)) {
                $class = substr($Class, strlen($namespace)+1);
            }

            $file = $this->pushReference(
                $this->nameSpace,
                $class,
                $directory . DIRECTORY_SEPARATOR . $class . '.php'
            );
            if ($file) {
                self::$classMapReference[$this->resolveNameSpaceLower($class)] = $file;
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
    protected function splitClassNameSpace(string $class) : array
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
    protected function getClassReference(string $class)
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
    protected function getGroupReference(string $group)
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
    protected function hasGroupReference(string $group) : bool
    {
        $group = $this->resolveNameSpaceLower($group);
        return isset(self::$references[$group]);
    }

    /**
     * @param $class
     */
    public function __invoke(string $class)
    {
        call_user_func_array([$this, 'load'], func_get_args());
    }
}

/**
 * @param string $file
 */
function IncludeFileOnce(string $file)
{
    /** @noinspection PhpIncludeInspection */
    include_once $file;
}
