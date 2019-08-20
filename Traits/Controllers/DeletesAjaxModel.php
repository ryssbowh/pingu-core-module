<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Entities\BaseModel;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait DeletesAjaxModel 
{
	use DeletesModel;
	
	/**
	 * @inheritDoc
	 */
	protected function onDeleteSuccess(BaseModel $model)
	{
		return ['message' => $model::friendlyName().' has been deleted'];
	}

	/**
	 * @inheritDoc
	 */
	protected function onDeleteFailure(BaseModel $model, \Exception $e)
	{
		if(env('APP_ENV') == 'local'){
			throw $e;
		}
		throw new HttpException(422, $model::friendlyName()." couldn't be deleted");
	}
}
