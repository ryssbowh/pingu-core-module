<?php
namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Uris extends Facade {

    protected static function getFacadeAccessor() {

        return 'core.uris';

    }

}