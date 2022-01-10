<?php

namespace Api;

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Exception;
use Phalcon\Mvc\Micro;

/**
 * Bootstrap application
 */
class Application
{
    /**
     * Phalcon micro app
     * @var Micro
     */
    protected Micro $app;

    /**
     * Dependency injection
     * @var Di
     */
    protected Di $di;

    /**
     * Source path
     * @var string
     */
    protected string $srcPath;

    /**
     * @param string $srcPath
     * @return void
     */
    public function __construct(string $srcPath)
    {
        $this->srcPath = $srcPath;
        $this->initDi();
        $this->initProviders();
        $this->initApp();
        $this->initRoutes();
        $this->initMiddlewares();
    }

    /**
     * Initialize dependency injection
     * @return void
     */
    protected function initDi(): void
    {
        $this->di = new FactoryDefault();
    }

    /**
     * Initialize providers
     * @return void
     */
    protected function initProviders(): void
    {
        // get providers config
        $providers = require_once $this->srcPath . '/config/providers.php';

        // set providers in dependency injection
        if (is_array($providers) && !empty($providers)) {
            foreach ($providers as $name => $provider) {
                $this->di->set($name, $provider);
            }
        }
    }

    /**
     * Initialize application
     * @return void
     */
    protected function initApp(): void
    {
        $this->app = new Micro($this->di);
    }

    /**
     * Initialize application routes
     * @return void
     */
    protected function initRoutes(): void
    {
        // get config routes
        $apiRoutes = include_once $this->srcPath . '/config/routes.php';

        // initialize collections routes
        if (is_array($apiRoutes) && !empty($apiRoutes['collections'])) {
            foreach ($apiRoutes['collections'] as $collection) {
                $this->app->mount($collection);
            }
        }

        // initialize not found route, throw exception to call error handler
        if (is_array($apiRoutes) && !empty($apiRoutes['error'])) {
            $this->app->notFound(function () {
                throw new Exception('Not Found', 404);
            });

            // initialize error route
            $this->app->error($apiRoutes['error']);
        }
    }

    /**
     * Initialize application middlewares
     * @return void
     */
    protected function initMiddlewares()
    {
        // get middlewares config
        $apiMiddlewares = include_once $this->srcPath . '/config/middlewares.php';

        // set middlewares in application
        if (is_array($apiMiddlewares) && !empty($apiMiddlewares)) {
            foreach ($apiMiddlewares as $middleware) {
                list('type' => $type, 'class' => $class) = $middleware;
                $this->app->$type(new $class);
            }
        }
    }

    /**
     * Run application
     * @return void
     */
    public function run()
    {
        $this->app->handle($_SERVER['REQUEST_URI'] ?? '/api/stats');
    }
}