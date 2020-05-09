<?php

namespace Pingu\Core\Traits\Models;

use Illuminate\Support\Str;
use Pingu\Core\Exceptions\ClassException;

trait HasPolicy
{
    /**
     * Boot trait
     */
    public static function bootHasPolicy()
    {
        static::registered(function ($model) {
            \Policies::register(get_class($model), $model->getPolicy());
        });
    }

    /**
     * @inheritDoc
     */
    public function getPolicy(): string
    {
        $class = base_namespace($this) . '\\Policies\\' . class_basename($this).'Policy';
        if (class_exists($class)) {
            return $class;
        }
        throw ClassException::missingDependency($this, $class);
    }
}