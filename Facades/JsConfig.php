<?php
namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class JsConfig extends Facade {

	protected static function getFacadeAccessor() {

		return 'core.jsconfig';

	}

}