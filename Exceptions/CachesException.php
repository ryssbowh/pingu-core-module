<?php

namespace Pingu\Core\Exceptions;

class CachesException extends \Exception
{
    public static function defined(string $name)
    {
        return new static("Cache name '$name' is already registered");
    }

    public static function notDefined(string $name)
    {
        return new static("Cache name '$name' is not defined");
    }
}