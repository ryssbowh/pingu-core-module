<?php

namespace Pingu\Core\Contracts;

use Pingu\Core\Support\Routes;

interface HasRoutesContract
{
    public static function routes(): Routes;
}