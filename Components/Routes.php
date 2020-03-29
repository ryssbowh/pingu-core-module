<?php

namespace Pingu\Core\Components;

use Pingu\Core\Support\Routes as RoutesSupport;

class Routes
{
    protected $routeInstances = [];

    public function register(string $class, RoutesSupport $routes)
    {
        $this->routeInstances[$class] = $routes;
    }

    public function get($object): RoutesSupport
    {
        if (is_object($object)) {
            $object = get_class($object);
        }
        if (isset($this->routeInstances[$object])) {
            return $this->routeInstances[$object];
        }
    }

    public function getAll()
    {
        return $this->routeInstances;
    }

    public function registerAll()
    {
        if (app()->routesAreCached()) {
            return;
        }
        foreach ($this->routeInstances as $routes) {
            $routes->register();
        }
    }
}