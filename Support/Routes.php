<?php

namespace Pingu\Core\Support;

use Illuminate\Support\Arr;
use Pingu\Core\Contracts\HasUrisContract;

abstract class Routes
{
    protected $routes = [];
    protected $methods = [];
    protected $middlewares = [];
    protected $names = [];
    protected $controllers = [];

    public function __construct()
    {
        $this->routes = $this->routes();
        $this->methods = $this->methods();
        $this->middlewares = $this->middlewares();
        $this->names = $this->names();
        $this->controllers = $this->controllers();
    }

    abstract public function register();

    protected function routes(): array
    {
        return [];
    }

    protected function methods(): array
    {
        return [];
    }

    protected function middlewares(): array
    {
        return [];
    }

    protected function names(): array
    {
        return [];
    }

    protected function controllers(): array
    {
        return [];
    }

    public function getRoutes(string $action = null)
    {
        if (!is_null($action)) {
            return $this->routes[$action] ?? null;
        }
        return $this->routes;
    }

    public function getMethods(string $action = null)
    {
        if (!is_null($action)) {
            return $this->methods[$action] ?? 'get';
        }
        return $this->methods;
    }

    public function getMiddlewares(string $action = null)
    {
        if (!is_null($action)) {
            return $this->middlewares[$action] ?? null;
        }
        return $this->middlewares;
    }

    public function getNames(string $prefix = null)
    {
        if (!is_null($prefix)) {
            return $this->names[$prefix] ?? null;
        }
        return $this->names;
    }

    public function getControllers(string $prefix = null)
    {
        if (!is_null($prefix)) {
            return $this->controllers[$prefix] ?? null;
        }
        return $this->controllers;
    }

    public function addRoute(string $group, string $action, string $method = 'get', $middleware = null, $name = null)
    {
        $this->routes[$group][] = $action;
        $this->methods[$action] = $method;
        if ($middleware) {
            $this->middlewares[$action] = $middleware;
        }
        if ($name) {
            $this->names[$group.'.'.$action] = $name;
        }
    }
}