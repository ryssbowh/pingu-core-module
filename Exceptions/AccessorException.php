<?php
namespace Pingu\Core\Exceptions;

use Pingu\Core\Support\Accessor;

class AccessorException extends \Exception{

    public static function undefined(string $accessor, Accessor $class)
    {
        return new static("Accessor '$accessor' isn't defined in ".get_class($class));
    }

    public static function defined(string $accessor, Accessor $class)
    {
        return new static("Accessor '$accessor' already defined in ".get_class($class));
    }
}