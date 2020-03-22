<?php

namespace Pingu\Core\Traits\Models;

use Illuminate\Support\Str;

trait HasFieldsFriendlyNames
{
    /**
     * Friendly value for an field
     * 
     * @param string $key
     * 
     * @return string
     */
    public static function getFriendlyFieldName($key): string
    {
        if (static::hasFriendlyFieldNameMutator($key)) {
            return $this->mutateFriendlyFieldName($key);
        }

        return static::getDefaultFriendlyFieldName($key);
    }

    /**
     * Detect whether key has field name mutator.
     *
     * @param string  $key
     *
     * @return bool
     */
    public static function hasFriendlyFieldNameMutator(string $key): bool
    {
        return \method_exists(static::class, 'friendly'.Str::studly($key).'FieldName');
    }

    /**
     * Default friendly field name
     * 
     * @param string $key
     * 
     * @return string
     */
    protected static function getDefaultFriendlyFieldName($key): string
    {
        return friendly_field_name($key);
    }

    /**
     * Mutate field name
     *
     * @param string  $key
     *
     * @return string
     */
    private static function mutateFriendlyFieldName($key): string
    {
        return static::{'friendly'.Str::studly($key).'FieldName'}();
    }
}