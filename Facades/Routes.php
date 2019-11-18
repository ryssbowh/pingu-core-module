<?php
namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Routes extends Facade {

    protected static function getFacadeAccessor() {

        return 'core.routes';

    }

}