<?php

namespace Pingu\Core\Support;

use Illuminate\Support\Arr;
use Pingu\Core\Contracts\HasUrisContract;

abstract class Routes
{
    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var array
     */
    protected $methods = [];

    /**
     * @var array
     */
    protected $middlewares = [];

    /**
     * @var array
     */
    protected $names = [];

    /**
     * @var array
     */
    protected $controllers = [];

    /**
     * @var array
     */
    protected $contexts = [];

    public function __construct()
    {
        $this->routes = $this->routes();
        $this->methods = $this->methods();
        $this->middlewares = $this->middlewares();
        $this->names = $this->names();
        $this->controllers = $this->controllers();
        $this->controllerActions = $this->controllerActions();
        $this->contexts = $this->contexts();
    }

    /**
     * Register routes
     */
    abstract public function register();

    /**
     * Routes defined by this class
     * 
     * @return array
     */
    protected function routes(): array
    {
        return [];
    }

    /**
     * Methods defined by this class
     * 
     * @return array
     */
    protected function methods(): array
    {
        return [];
    }

    /**
     * Routes middlewares defined by this class
     * 
     * @return array
     */
    protected function middlewares(): array
    {
        return [];
    }

    /**
     * Routes names defined by this class
     * 
     * @return array
     */
    protected function names(): array
    {
        return [];
    }

    /**
     * Route controllers defined by this class
     * 
     * @return array
     */
    protected function controllers(): array
    {
        return [];
    }

    /**
     * Route controllers actions defined by this class
     * 
     * @return array
     */
    protected function controllerActions(): array
    {
        return [];
    }

    /**
     * Route contexts defined by this class
     * 
     * @return array
     */
    protected function contexts(): array
    {
        return [];
    }

    /**
     * Route getter
     * 
     * @param string|null $action
     * 
     * @return string|array|null
     */
    public function getRoute(string $action = null)
    {
        if (!is_null($action)) {
            return $this->routes[$action] ?? null;
        }
        return $this->routes;
    }

    /**
     * Method getter
     * 
     * @param string|null $action
     * 
     * @return string|array
     */
    public function getMethod(string $action = null)
    {
        if (!is_null($action)) {
            return $this->methods[$action] ?? 'get';
        }
        return $this->methods;
    }

    /**
     * Middleware getter
     * 
     * @param string|null $action|null
     * 
     * @return string|array|null
     */
    public function getMiddleware(string $action = null)
    {
        if (!is_null($action)) {
            return $this->middlewares[$action] ?? null;
        }
        return $this->middlewares;
    }

    /**
     * Name getter
     * 
     * @param string|null $action
     * 
     * @return string|array|null
     */
    public function getName(string $prefix = null)
    {
        if (!is_null($prefix)) {
            return $this->names[$prefix] ?? null;
        }
        return $this->names;
    }

    /**
     * Controller getter
     * 
     * @param string|null $action
     * 
     * @return string|array|null
     */
    public function getController(string $prefix = null)
    {
        if (!is_null($prefix)) {
            return $this->controllers[$prefix] ?? null;
        }
        return $this->controllers;
    }

    /**
     * Controller getter
     * 
     * @param string|null $action
     * 
     * @return string|array
     */
    public function getControllerAction(string $action = null)
    {
        if (!is_null($action)) {
            return $this->controllerActions[$action] ?? $action;
        }
        return $this->controllerActions;
    }

    /**
     * Context getter. Will return a default one if not defined in this repository.
     * If the path is 'admin.index', the context returned will be
     * ['admin.index', 'index']
     * 
     * @param string|null $path
     * 
     * @return string|array|null
     */
    public function getContext(string $path = null)
    {
        if (is_null($path)) {
            return $this->contexts;
        }
        if (isset($this->contexts[$path])) {
            return $this->contexts[$path];
        }
        $elems = explode('.', $path);
        return [$path, end($elems)];
    }

    /**
     * Add a route
     * 
     * @param string       $group
     * @param string       $action
     * @param string       $method     
     * @param string|array $middleware 
     * @param ?string      $context    
     * @param ?string      $name      
     */
    public function addRoute(string $group, string $action, string $method = 'get', $middleware = null, ?string $context = null, ?string $name = null)
    {
        $this->routes[$group][] = $action;
        $this->methods[$action] = $method;
        if ($middleware) {
            $this->middlewares[$action] = $middleware;
        }
        if ($name) {
            $this->names[$group.'.'.$action] = $name;
        }
        if ($context) {
            $this->contexts[$group.'.'.$action] = $context;
        }
    }
}