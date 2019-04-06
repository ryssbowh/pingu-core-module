<?php
namespace Modules\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Notify extends Facade {

	protected static function getFacadeAccessor() {

		return 'core.notify';

	}

}