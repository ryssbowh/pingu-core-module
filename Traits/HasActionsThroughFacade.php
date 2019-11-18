<?php

namespace Pingu\Core\Traits;

use Pingu\Core\Support\Actions;

trait HasActionsThroughFacade
{
    public static function actions(): Actions
    {
        return \Actions::get(static::class);
    }
}