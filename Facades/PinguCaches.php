<?php
namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class PinguCaches extends Facade
{

    protected static function getFacadeAccessor()
    {

        return 'core.caches';

    }

}