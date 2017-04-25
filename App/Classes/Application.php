<?php
namespace PentagonalProject\ProjectSeventh;

use Apatis\Exceptions\LogicException;
use PentagonalProject\ProjectSeventh\Utilities\EmbeddedCollection;
use Slim\App;
use Slim\Container;
use Slim\Http\Environment;

/**
 * Class Application
 * @package PentagonalProject\ProjectSeventh
 */
class Application
{
    /**
     * @var string
     */
    protected $rootDirectory;

    /**
     * @var string
     */
    protected $appDirectory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var App
     */
    protected $slim;

    /**
     * @var bool
     */
    protected $hasRun;

    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->appDirectory  = dirname(__DIR__);
        $this->rootDirectory = dirname($this->appDirectory);
        $this->init($config);
    }

    /**
     * Get Root Directory
     *
     * @return string
     */
    public function getRootDirectory() : string
    {
        return $this->rootDirectory;
    }

    /**
     * Get Application Directory
     *
     * @return string
     */
    public function getAppDirectory() : string
    {
        return $this->appDirectory;
    }

    /**
     * Initialize
     *
     * @param $config
     */
    protected function init(array &$config)
    {
        $config['directory'] = isset($config['directory']) ? $config['directory'] : [];
        if (!is_array($config['directory'])) {
            $config['directory'] =  [];
        }
        $config['directory'] = array_merge([
            'extension' => WEB_ROOT . DIRECTORY_SEPARATOR . 'extensions',
            'module'    => $this->getRootDirectory() .DIRECTORY_SEPARATOR . ' Modules',
            'storage'   => $this->getRootDirectory() .DIRECTORY_SEPARATOR . ' Storage',
        ], $config['directory']);

        $config['httpVersion'] = isset($_SERVER['SERVER_PROTOCOL'])
            && strpos($_SERVER['SERVER_PROTOCOL'], '/') !== false
            ? explode('/', $_SERVER['SERVER_PROTOCOL'])[1]
            : '1.1';

        $this->config = new Config($config);
    }

    /**
     * Run The application
     *
     * @return Application
     */
    public function process()
    {
        if ($this->hasRun) {
            throw new LogicException(
                'Application has been run! Please does not re run the application procedure.',
                E_ERROR
            );
        }

        $c =& $this;
        $this->slim = new App(
            [
                /**
                 * Application Instance
                 *
                 * Use on closure prevent being binding to
                 * @return Application
                 */
                'application' => function () use (&$c) : Application {
                    return $c;
                },
                /**
                 * Configuration Container
                 *
                 * Use on closure prevent being binding to
                 * @return Config
                 */
                'config' => function () use (&$c) : Config {
                    return $c->config;
                },
                /**
                 * Closure
                 *
                 * @return Database
                 */
                'database'    => require_once dirname(__DIR__) . '/Containers/Database.php',
                /**
                 * Closure
                 *
                 * @return Environment
                 */
                'environment' => require dirname(__DIR__) . '/Containers/Environment.php',
                /**
                 * Module Container
                 *
                 * Closure
                 *
                 * @return EmbeddedCollection
                 */
                'module'      => require_once dirname(__DIR__) . '/Containers/Module.php',
                /**
                 * Extension Container
                 *
                 * Closure
                 *
                 * @return EmbeddedCollection
                 */
                'extension'    => require_once dirname(__DIR__) . '/Containers/Extension.php',
                /**
                 * @return array
                 */
                'settings'    => require dirname(__DIR__) . '/Containers/Settings.php',
                /**
                 * Slim Inheritance
                 *
                 * @return App
                 */
                'slim'        => function () use (&$c) : App {
                    return $c->slim;
                }
            ]
        );

        $this->hasRun = true;
        $this->slim->any('/[{param: .+}]', function($request, $response) {
            /** @var EmbeddedCollection $module */
            $module = $this['module'];
            $ret = print_r($module, true);
            $body = $response->getBody();
            $body->write($ret);
            $response->withBody($body);
            return $response;
        });
        $this->slim->run();

        return $this;
    }
}
