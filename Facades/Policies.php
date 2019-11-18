<?php
namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Policies extends Facade {

    protected static function getFacadeAccessor() {

        return 'core.policies';

    }

}