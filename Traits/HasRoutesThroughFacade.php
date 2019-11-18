<?php

namespace Pingu\Core\Traits;

use Pingu\Core\Support\Routes;

trait HasRoutesThroughFacade
{
    public static function routes(): Routes
    {
        return \Routes::get(static::class);
    }
}