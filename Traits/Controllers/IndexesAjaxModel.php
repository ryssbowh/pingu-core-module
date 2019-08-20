<?php

namespace Pingu\Core\Traits\Controllers;

use Illuminate\Database\Eloquent\Collection;

trait IndexesAjaxModel 
{
	use IndexesModel;

	/**
	 * @inheritDoc
	 */
	protected function onIndexSuccess(Colection $models)
	{
		return ['models' => $models->toArray(), 'total' => $count];
	}
}
