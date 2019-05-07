<?php
namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class TextSnippet extends Facade {

	protected static function getFacadeAccessor() {

		return 'core.textSnippet';

	}

}