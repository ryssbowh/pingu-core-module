<?php

namespace Pingu\Core\Contracts;

use Pingu\Core\Support\Accessor;

interface HasAccessorsContract
{
    public static function accessor(): Accessor;
}