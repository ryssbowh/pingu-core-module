<?php
namespace Pingu\Core\Exceptions;

use Pingu\Core\Support\Uris\Uris;

class UriException extends \Exception
{

    public static function undefined(string $uri, Uris $class)
    {
        return new static("Uri '$uri' isn't defined in ".get_class($class));
    }

    public static function defined(string $uri, Uris $class)
    {
        return new static("Uri '$uri' already defined in ".get_class($class));
    }
}