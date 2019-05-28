<?php
namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class ThemeConfig extends Facade {

	protected static function getFacadeAccessor() {

		return 'core.themeConfig';

	}

}