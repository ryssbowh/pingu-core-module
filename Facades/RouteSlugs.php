<?php
namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class RouteSlugs extends Facade
{

    protected static function getFacadeAccessor()
    {

        return 'core.routeSlugs';

    }

}