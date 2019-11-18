<?php

namespace Pingu\Core\Exceptions;

class RouteActionNotSet extends \Exception
{
    public function __construct($controller, $name)
    {
        parent::__construct("'$name' must be set within route actions to use controller ".get_class($controller));
    }
}