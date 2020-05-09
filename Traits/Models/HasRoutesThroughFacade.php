<?php

namespace Pingu\Core\Traits\Models;

use Pingu\Core\Support\Routes;

trait HasRoutesThroughFacade
{
    /**
     * Boot trait
     */
    public static function bootHasRoutesThroughFacade()
    {
        static::registered(function ($model) {
            \Routes::register(get_class($model), $model->getRoutesInstance());
        });
    }

    /**
     * Routes instance for this model
     * 
     * @return Routes
     */
    protected abstract function defaultRouteInstance(): Routes;

    /**
     * Route instance accessor
     * 
     * @return Routes
     */
    public static function routes(): Routes
    {
        return \Routes::get(static::class);
    }

    /**
     * Routes instance for this model
     * 
     * @return Routes
     */
    public function getRoutesInstance(): Routes
    {
        $class = base_namespace($this) . '\\Routes\\' . class_basename($this).'Routes';
        if (class_exists($class)) {
            return new $class($this);
        }
        return $this->defaultRouteInstance();
    }
}