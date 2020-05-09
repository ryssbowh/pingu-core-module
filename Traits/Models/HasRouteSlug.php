<?php

namespace Pingu\Core\Traits\Models;

use Illuminate\Support\Str;

trait HasRouteSlug
{
    /**
     * Boot trait
     */
    public static function bootHasRouteSlug()
    {
        static::registered(function ($model) {
            if ($model->shouldRegisterRouteSlug()) {
                \RouteSlugs::registerSlugFromObject($model);
            }
        });
    }

    /**
     * Should this class register its route slug.
     * This can be turned off if the model gets his route slug from a parent class.
     * 
     * @return bool
     */
    public function shouldRegisterRouteSlug(): bool
    {
        return true;
    }

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
        return class_machine_name(static::class);
    }

}