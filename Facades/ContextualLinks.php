<?php
namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class ContextualLinks extends Facade {

	protected static function getFacadeAccessor() {

		return 'core.contextualLinks';

	}

}