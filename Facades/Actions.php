<?php
namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Actions extends Facade
{

    protected static function getFacadeAccessor()
    {

        return 'core.actions';

    }

}