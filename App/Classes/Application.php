<?php
namespace PentagonalProject\ProjectSeventh;

use Apatis\Exceptions\InvalidArgumentException;
use Apatis\Exceptions\LogicException;
use PentagonalProject\ProjectSeventh\Exceptions\FileNotFoundException;
use Slim\App;

/**
 * Class Application
 * @package PentagonalProject\ProjectSeventh
 */
class Application
{
    /**
     * Application Key Name Selector
     *
     * @var string
     */
    const APP_KEY = 'application';

    /**
     * @var string
     */
    protected $rootDirectory;

    /**
     * @var string __DIR__
     */
    protected $appDirectory;

    /**
     * Component Directory
     *
     * @var string
     */
    protected $componentDirectory;

    /**
     * @var string
     */
    protected $webRootDirectory;

    /**
     * @var string
     */
    protected $containerDirectory;

    /**
     * @var App
     */
    protected $slim;

    /**
     * @var bool
     */
    protected $hasRun;

    /**
     * @var string
     */
    protected $ds = DIRECTORY_SEPARATOR;

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->appDirectory  = dirname(__DIR__);
        $this->componentDirectory = $this->getAppDirectory('Components');
        $this->containerDirectory = $this->getAppDirectory('Containers');
        $this->rootDirectory = dirname($this->appDirectory);
        if (!defined('WEB_ROOT')) {
            define('WEB_ROOT', dirname($_SERVER['SCRIPT_FILENAME']));
        }
        $this->webRootDirectory = $this->getFixPath(WEB_ROOT, false);
    }

    /**
     * Returning Directory Separator
     *
     * @return string
     */
    public function getDS()
    {
        return $this->ds;
    }

    /**
     * Fix Path Separator
     *
     * @param string $path
     * @param bool   $useCleanPrefix
     * @return string
     */
    public function getFixPath(string $path = '', $useCleanPrefix = false) : string
    {
        /**
         * Trimming path string
         */
        if (($path = trim($path)) == '') {
            return $path;
        }

        $path = preg_replace('`(\/|\\\)+`', $this->getDS(), $path);
        if ($useCleanPrefix) {
            $path = $this->getDS() . ltrim($path, $this->getDS());
        }

        return $path;
    }

    /**
     * Get Root Directory
     *
     * @param string $path
     * @return string
     */
    public function getRootDirectory(string $path = '') : string
    {
        return $this->rootDirectory . $this->getFixPath($path, true);
    }

    /**
     * Get Web Root Directory
     *
     * @param string $path
     * @return string
     */
    public function getWebRootDirectory(string $path = '') : string
    {
        return $this->webRootDirectory . $this->getFixPath($path, true);
    }

    /**
     * Get Application Directory
     *
     * @param string $path
     * @return string
     */
    public function getAppDirectory(string $path = '') : string
    {
        return $this->appDirectory . $this->getFixPath($path, true);
    }

    /**
     * Get Application Directory
     *
     * @param string $path
     * @return string
     */
    public function getContainerDirectory(string $path = '') : string
    {
        return $this->containerDirectory . $this->getFixPath($path, true);
    }

    /**
     * Get Application Component Directory
     * @param string $path
     * @return string
     */
    public function getComponentDirectory(string $path = '') : string
    {
        return $this->componentDirectory . $this->getFixPath($path, true);
    }

    /**
     * Include Scope
     *
     * @param-read string $file
     * @return mixed
     * @throws FileNotFoundException
     */
    public function includeScope()
    {
        if (func_num_args() < 1) {
            throw new InvalidArgumentException(
                'Argument 1 could not be empty.',
                E_USER_ERROR
            );
        }

        if (!is_string(func_get_arg(0))) {
            throw new InvalidArgumentException(
                sprintf(
                    'Argument 1 must be as a string %s given.',
                    gettype(func_get_arg(0))
                ),
                E_USER_ERROR
            );
        }

        if (!($path = stream_resolve_include_path(func_get_arg(0)))) {
            throw new FileNotFoundException(
                func_get_arg(0)
            );
        }

        /**
         * closure include of scope to prevent access @uses Application
         * bind to @uses Arguments
         * if inside of include call $this it wil be access as @uses Arguments object
         * @uses Application::APP_KEY to access application instance
         * eg :
         *  $this->get(Application::APP_KEY)
         */
        $args = func_get_args();
        $args[self::APP_KEY] =& $this;
        $fn = (function () {
            /** @var Arguments $this */
            /** @noinspection PhpIncludeInspection */
            return include $this[0];
        })->bindTo(new Arguments($args));

        return $fn();
    }

    /**
     * Include Scope
     *
     * @param-read string $file
     * @return mixed
     * @throws FileNotFoundException
     */
    public function includeScopeOnce()
    {
        if (func_num_args() < 1) {
            throw new InvalidArgumentException(
                'Argument 1 could not be empty.',
                E_USER_ERROR
            );
        }

        if (!is_string(func_get_arg(0))) {
            throw new InvalidArgumentException(
                sprintf(
                    'Argument 1 must be as a string %s given.',
                    gettype(func_get_arg(0))
                ),
                E_USER_ERROR
            );
        }

        if (!($path = stream_resolve_include_path(func_get_arg(0)))) {
            throw new FileNotFoundException(
                func_get_arg(0)
            );
        }

        /**
         * closure include of scope to prevent access @uses Application
         * bind to @uses Arguments
         * if inside of include call $this it wil be access as @uses Arguments object
         * @uses Application::APP_KEY to access application instance
         * eg :
         *  $this->get(Application::APP_KEY)
         */
        $args = func_get_args();
        $args[self::APP_KEY] =& $this;
        $fn = (function () {
            /** @var Arguments $this */
            /** @noinspection PhpIncludeInspection */
            return include_once $this[0];
        })->bindTo(new Arguments($args));

        return $fn();
    }

    /**
     * Get Run Slim Instance
     *
     * @return App
     */
    public function &getSlim()
    {
        return $this->slim;
    }

    /**
     * Run The application
     *
     * @param array $config
     * @return Application
     */
    public function process(array $config)
    {
        if ($this->hasRun) {
            throw new LogicException(
                'Application has been run! Please does not re run the application procedure.',
                E_ERROR
            );
        }

        // must be call it first
        $this->slim = $this->includeScope(
            $this->getComponentDirectory('ApplicationSlimObject.php'),
            $config
        );
        // call middleware
        $this->includeScope($this->getComponentDirectory('ApplicationMiddleware.php'));
        // determine & call routes
        $this->includeScope($this->getComponentDirectory('ApplicationRoutes.php'));
        $this->slim->run();
        return $this;
    }
}
