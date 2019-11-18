<?php

namespace Pingu\Core\Traits\Models;

use Illuminate\Support\Str;

trait HasRouteSlug
{
    /**
     * Route slug (plural)
     * 
     * @return string
     */
    public static function routeSlugs(): string
    {
        return str_plural(static::routeSlug());
    }

    /**
     * Route slug (singular)
     * 
     * @return string
     */
    public static function routeSlug(): string
    {
        return Str::snake(class_basename(static::class));
    }

}