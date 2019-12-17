<?php

namespace Pingu\Core\Exceptions;

class ParameterMissing extends \Exception
{

    public function __construct($field)
    {
        parent::__construct("parameter $field missing in request");
    }

}