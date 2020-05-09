<?php

namespace Pingu\Core\Exceptions;

use Pingu\Entity\Support\Entity;

class RouteContextException extends \Exception
{
    public static function undefined(string $name, $object)
    {
        return new static("Context '$name' is not defined in ".get_class($object));
    }
}