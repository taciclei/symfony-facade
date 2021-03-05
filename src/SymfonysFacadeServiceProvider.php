<?php namespace Taciclei\SymfonysFacade;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;
use Taciclei\SymfonysFacade\Facades\Commands\SymfonyCommandsFacade;
use Taciclei\SymfonysFacade\Facades\Routes\LaraverRouteBuilder;
use Taciclei\SymfonysFacade\Facades\Routes\SymfonyRoutesManager;
use Taciclei\SymfonysFacade\Services\Symfony\SymfonyContainer;

class SymfonysFacadeServiceProvider extends ServiceProvider
{
    private $symfonyContainer;

    private $routebuilder;

    private $symfonyCommandsFacade;

    /** @var SymfonyRoutesManager $routeManager */
    public $routeManager;

    /**
     * Boot package.
     */
    public function boot()
    {
        $this->symfonyContainer = new SymfonyContainer(App::environment(), false);

        $this->routebuilder = new LaraverRouteBuilder();

        $this->routeManager = new SymfonyRoutesManager($this->symfonyContainer, $this->routebuilder);

        $this->symfonyCommandsFacade = new SymfonyCommandsFacade($this->symfonyContainer);

        //$this->loadAutoloader(base_path('packages'));
        $this->setupRoutes($this->app->router);
        $this->loadViewsFrom(realpath(__DIR__ . '/../views'), 'SymfonysFacade');
        $this->registerCommands();

        $this->publishes([__DIR__.'/config/symfo.php' => config_path('symfo.php')]);
    }

    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind('Taciclei\SymfonysFacade\Services\Symfony\SymfonyContainer', function () {
            return $this->symfonyContainer;
        });

        $this->app->bind('Taciclei\SymfonysFacade\Facades\Routes\SymfonyRoutesManager', function () {
            return $this->routeManager;
        });

        $this->app->bind('Taciclei\SymfonysFacade\Facades\Routes\LaraverRouteBuilder', function () {
            return $this->routebuilder;
        });

        $this->app->bind('Taciclei\SymfonysFacade\Facades\Commands\SymfonyCommandsFacade', function () {
            return $this->symfonyCommandsFacade;
        });
    }

    /**
     * Define the routes for the application.
     *
     * @param Router $router
     */
    public function setupRoutes(Router $router)
    {
        $router->group(['namespace' => 'Taciclei\SymfonysFacade\Controllers'], function () {
            require __DIR__ . '/Http/routes.php';
        });

       $this->routeManager->addSymfonyRoutes($router);
    }

    /**
     * Require composer's autoload file the packages.
     **/
    protected function loadAutoloader($path)
    {
        $finder = new Finder;
        $files = new Filesystem;

        $autoloads = $finder->in($path)->files()->name('autoload.php')->depth('<= 3')->followLinks();

        foreach ($autoloads as $file) {
            $files->requireOnce($file->getRealPath());
        }
    }

    /**
     * Registers commands facade.
     */
    public function registerCommands() {
        //$this->commands(['Taciclei\SymfonysFacade\Commands\SymfonyCommand']);
    }
}
