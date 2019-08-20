<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Entities\BaseModel;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait StoresAjaxModel 
{
	use StoresModel;

	/**
	 * @inheritDoc
	 */
	protected function onStoreSuccess(BaseModel $model)
	{
		return ['model' => $model, 'message' => $model::friendlyName()." has been created"];
	}
}
