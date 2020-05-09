<?php
namespace Pingu\Core\Exceptions;

class ClassException extends \Exception
{

    public static function missingInterface(string $class, string $interface)
    {
        return new static($class." must implement ".$interface);
    }

    public static function missingDependency(BaseModel $model, string $class)
    {
        return new static(get_class($model).' must define the following class : '.$class);
    }

}