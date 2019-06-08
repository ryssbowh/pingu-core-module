<?php

namespace Pingu\Core\Traits\Models;

use Illuminate\Support\Str;

trait HasRouteSlug
{
	/**
     * Route slugs (plural)
     * @return string
     */
    public static function routeSlugs()
    {
        return str_plural(static::routeSlug());
    }

    /**
     * Route slug (singular)
     * @return string
     */
    public static function routeSlug()
    {
        return Str::snake(class_basename(static::class));
    }

}