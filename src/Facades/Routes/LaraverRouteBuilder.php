<?php namespace Phpjit\SymfonysFacade\Facades\Routes;

use Illuminate\Routing\Route;
use Phpjit\SymfonysFacade\Controllers\FacadeController;

class LaraverRouteBuilder
{

    public $classFacadeController = FacadeController::class;

    private $routes;

    public $prefix;

    /**
     * LaraverRouteBuilder constructor.
     */
    public function __construct()
    {
        $this->prefix = $this->classFacadeController;
    }


    public function convertRoutes($routes)
    {
        $return = [];

        foreach ($routes as  $name => $route) {
            $action = '__invoke';

            $id = md5($route->getPath().':'.$name);
            $trr['action']['controller'] =  $this->prefix;
            $trr['action']['as'] = $name;
            $trr['action']['uses'] =  $this->prefix;
            $trr['action']['id'] =  $id;

            $trr['defaults'] = $route;
            $trr['methods'] = $this->transformMethods($route);
            $trr['path'] = $route->getPath();
            $trr['name'] = $name;

            if(!empty($route->getDefaults()["_controller"])) {
                $trr["_controller"] = $route->getDefaults()["_controller"];
            }

            $return[$id] = $trr;
        }

        $this->routes = $return;

        return $return;
    }

    private function getExplodedController($route)
    {
        if(!empty($route->getDefaults())) {
            $controllerDirty = explode('::', $route->getDefaults()["_controller"]);
            //if(count($controllerDirty)) {
                //$controllerDirty = explode('.', $route->getDefaults()["_controller"]);
            //}

            return $controllerDirty;
        }

        return [];
    }

    private function transformMethods($route)
    {
        $methods = $route->getMethods();
        if (count($methods) < 1) {
            $methods = ['get', 'post'];
        }

        return $methods;
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}
