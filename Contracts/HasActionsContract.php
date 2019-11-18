<?php

namespace Pingu\Core\Contracts;

use Pingu\Core\Support\Actions;

interface HasActionsContract
{
    public static function actions(): Actions;
}