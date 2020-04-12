<?php

namespace Pingu\Core\Exceptions;

use Pingu\Core\Support\Actions;

class ActionsException extends \Exception
{
    public static function undefined(string $name, Actions $class)
    {
        return new static("Action '$name' isn't defined in ".get_class($class));
    }

    public static function defined(string $name, Actions $class)
    {
        return new static("Action '$name' already defined in ".get_class($class));
    }
}