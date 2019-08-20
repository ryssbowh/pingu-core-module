<?php

namespace Pingu\Core\Traits\Controllers;

use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait PatchesAjaxModel 
{
	use PatchesModel;

	/**
	 * @inheritDoc
	 */
	protected function onPatchSuccess(Collection $models)
	{
		return ['models' => $models->toArray(), 'message' => $this->model::friendlyNames().' have been saved'];
	}

}
