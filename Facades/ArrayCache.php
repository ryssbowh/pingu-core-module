<?php
namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class ArrayCache extends Facade
{

    protected static function getFacadeAccessor()
    {

        return 'core.arrayCache';

    }

}