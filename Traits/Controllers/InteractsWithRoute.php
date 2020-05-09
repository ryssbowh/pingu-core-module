<?php

namespace Pingu\Core\Traits\Controllers;

use Illuminate\Routing\Route;
use Pingu\Core\Exceptions\RouteActionNotSet;
use Pingu\Core\Exceptions\RouteParameterException;

trait InteractsWithRoute
{
    /**
     * Route getter
     * 
     * @return Route
     */
    protected function getRoute(): Route
    {
        return $this->request->route();
    }

    /**
     * Prefix the store uri
     * 
     * @return string
     */
    protected function getRouteScope(): string
    {
        return $this->getRouteAction('scope');
    }

    /**
     * Requires a route parameter
     * 
     * @param string|int $key
     * 
     * @return mixed
     */
    protected function routeParameter(string $key)
    {
        if (is_int($key)) {
            $parameters = array_keys($this->getRoute()->parameters);
            if (isset($parameters[$key])) {
                $key = $parameters[$key];
            }
        }
        if ($key and isset($this->getRoute()->parameters[$key])) {
            return $this->getRoute()->parameters[$key];
        }
        throw new RouteParameterException($key);
    }

    /**
     * Requires a route action
     * 
     * @param string $name
     * 
     * @return mixed
     */
    protected function getRouteAction(string $name)
    {
        $actions = $this->getRoute()->action;
        if (!isset($actions[$name])) {
            throw new RouteActionNotSet($this, $name);
        }
        return $actions[$name];
    }
} 