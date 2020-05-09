<?php

namespace Pingu\Core\Exceptions;

class ContextException extends \Exception
{
    public static function undefined(string $scope, object $object)
    {
        return new static("Default context for scope '$scope' is not defined for ".get_class($object));
    }

    public static function nothingDefined(object $object, array $scopes)
    {
        return new static("Couldn't find a defined scope in the list (".implode(',', $scopes).") for ".get_class($object));
    }

    public static function noRequest(RouteContextContract $context)
    {
        return new static("Request is not set for context ".get_class($context).". Use \$context->setRequest(Request \$request) before asking for a response");
    }
}