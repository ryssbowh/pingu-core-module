<?php

namespace Pingu\Core\Traits;

use Pingu\Core\Support\Uris;

trait HasUrisThroughFacade
{
    public static function uris(): Uris
    {
        return \Uris::get(static::class);
    }
}