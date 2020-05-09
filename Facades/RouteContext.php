<?php

namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class RouteContext extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'core.routeContext';
    }

}