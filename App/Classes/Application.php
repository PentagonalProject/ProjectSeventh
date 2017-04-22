<?php
namespace PentagonalProject\ProjectSeventh;

use Slim\App;

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
     * @return string
     */
    public function getRootDirectory() : string
    {
        return $this->rootDirectory;
    }

    /**
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
    protected function init(array $config)
    {
        $config['directory'] = isset($config['directory']) ? $config['directory'] : [];
        if (!is_array($config['directory'])) {
            $config['directory'] =  [];
        }
        $config['directory'] = array_merge([
            'module' => $this->getRootDirectory() .DIRECTORY_SEPARATOR . ' Modules',
            'storage' => $this->getRootDirectory() .DIRECTORY_SEPARATOR . ' Storage'
        ], $config['directory']);

        $config['httpVersion'] = isset($_SERVER['SERVER_PROTOCOL'])
            && strpos($_SERVER['SERVER_PROTOCOL'], '/') !== false
            ? explode('/', $_SERVER['SERVER_PROTOCOL'])[1]
            : '1.1';

        $this->config = new Config($config);
    }

    public function run()
    {
        $this->slim = new App(
            [
                'module'   => function($c) {
                    return (new ModuleCollection($c['settings']['directory']['module']))->scan();
                },
                'config' => function($c) {
                    $this->config = new Config($c['settings']);
                    return $this->config;
                },
                'settings' => $this->config->get()
            ]
        );

        return $this->slim->run();
    }
}
