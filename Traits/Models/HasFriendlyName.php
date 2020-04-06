<?php

namespace Pingu\Core\Traits\Models;

use Illuminate\Support\Str;

trait HasFriendlyName
{
    /**
     * Model's friendly name
     * 
     * @return string
     */
    public static function friendlyName(): string
    {
        return friendly_classname(static::class);
    }

    /**
     * Model's friendly names
     * 
     * @return string
     */
    public static function friendlyNames(): string
    {
        return str_plural(static::friendlyName());
    }
}