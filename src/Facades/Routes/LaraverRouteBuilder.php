<?php namespace Taciclei\SymfonysFacade\Facades\Routes;

use Illuminate\Routing\Route;

class LaraverRouteBuilder
{

    public $prefix = 'Taciclei\SymfonysFacade\Controllers\FacadeController@';

    private $routes;

    public function convertRoutes($routes)
    {
        $return = [];

        foreach ($routes as  $name => $route) {
            $action = null;

            if (isset($this->getTransformedController($route)[2])) {
                // Usual Controller.
                $bundle = $controller = $this->getTransformedController($route)[0];
                $controller = $this->getTransformedController($route)[1];
                $action = $this->getTransformedController($route)[2];
            } else {
                // Controller as a service.
                $bundle = 'service';
                if(!empty($this->getTransformedController($route))) {
                    $controller = $this->getTransformedController($route)[0];
                    $action = $this->getTransformedController($route)[1];
                }

            }

            $trr['action']['controller'] =  $this->prefix . $controller . $action;
            $trr['action']['as'] = $name;
            $trr['action']['uses'] =  $this->prefix . $controller . ':' .  $action;

            $trr["symfony_bundle"] = $bundle;
            $trr["symfony_action"] = $action;
            $trr["symfony_service"] = $action;
            $trr["symfony_route_name"] = $name;
            $trr["symfony_controller"] = $controller;
            if(!empty($route->getDefaults()["_controller"])) {
                $trr["_controller"] = $route->getDefaults()["_controller"];
            }
            $trr['methods'] = $this->transformMethods($route);
            $trr['path'] = $route->getPath();
            $trr['name'] = $name;

            $return[] = $trr;
        }

        $this->routes = $return;

        return $return;
    }

    private function getTransformedController($route)
    {
        if(!empty($route->getDefaults())) {
            $controllerDirty = explode(':', $route->getDefaults()["_controller"]);
            if(count($controllerDirty)) {
                $controllerDirty = explode('.', $route->getDefaults()["_controller"]);
            }

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
