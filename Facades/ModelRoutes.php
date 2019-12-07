<?php
namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class ModelRoutes extends Facade
{

    protected static function getFacadeAccessor()
    {

        return 'core.modelRoutes';

    }

}