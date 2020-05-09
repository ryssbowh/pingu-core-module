<?php

namespace Pingu\Core\Traits\Models;

use Pingu\Core\Contracts\ActionRepositoryContract;

trait HasActionsThroughFacade
{
    /**
     * Default instance for actions
     * 
     * @return Actions
     */
    protected abstract function defaultActionsInstance(): ActionRepositoryContract;

    /**
     * Boot trait
     */
    public static function bootHasActionsThroughFacade()
    {
        static::registered(function ($model) {
            \Actions::register(get_class($model), $model->getActionsInstance());
        });
    }

    /**
     * Actions instance accessor
     * 
     * @return ActionRepositoryContract
     */
    public static function actions(): ActionRepositoryContract
    {
        return \Actions::get(static::class);
    }

    /**
     * Actions instance for this model
     * 
     * @return ActionRepositoryContract
     */
    public function getActionsInstance(): ActionRepositoryContract
    {
        $class = base_namespace($this) . '\\Actions\\' . class_basename($this).'Actions';
        if (class_exists($class)) {
            return new $class;
        }
        return $this->defaultActionsInstance();
    }
}