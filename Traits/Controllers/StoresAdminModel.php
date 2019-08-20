<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Entities\BaseModel;

trait StoresAdminModel
{
	use StoresModel;

	/**
	 * @inheritDoc
	 */
	protected function onStoreSuccess(BaseModel $model)
	{
		return redirect($model::makeUri('index', [], adminPrefix()));
	}

	/**
	 * @inheritDoc
	 */
	protected function afterStoreSuccess(BaseModel $model)
	{
		\Notify::success($model::friendlyName().' has been created');
	}

	/**
	 * @inheritDoc
	 */
	protected function onStoreFailure(BaseModel $model, $exception)
	{
		if(env('APP_ENV') == 'local'){
			throw $exception;
		}
		\Notify::danger('Error while creating '.$exception->getMessage());
		return back();
	}

}
