<?php
namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Notify extends Facade
{

    protected static function getFacadeAccessor()
    {

        return 'core.notify';

    }

}