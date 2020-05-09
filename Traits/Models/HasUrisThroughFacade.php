<?php

namespace Pingu\Core\Traits\Models;

use Pingu\Core\Support\Uris\Uris;

trait HasUrisThroughFacade
{
    /**
     * Default instance for routes
     * 
     * @return Routes
     */
    protected static abstract function defaultUrisInstance(): Uris;

    /**
     * Uris instance accessor
     * 
     * @return Uris
     */
    public static function uris(): Uris
    {
        return \Uris::get(static::class);
    }

    /**
     * Uris instance for this model
     * 
     * @return Uris
     */
    public static function makeUrisInstance(): Uris
    {
        $class = base_namespace(static::class) . '\\Uris\\' . class_basename(static::class).'Uris';
        if (class_exists($class)) {
            return new $class(static::class);
        }
        return static::defaultUrisInstance();
    }

    /**
     * Register Uris manually
     */
    public static function registerUris()
    {
        \Uris::register(static::class, static::makeUrisInstance());
    }
}