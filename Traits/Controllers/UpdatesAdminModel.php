<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Entities\BaseModel;

trait UpdatesAdminModel
{
	use UpdatesModel;

	/**
	 * @inheritDoc
	 */
	protected function onUpdateSuccess(BaseModel $model)
	{
		return redirect($model::makeUri('index', [], adminPrefix()));
	}

	/**
	 * @inheritDoc
	 */
	protected function onUpdateFailure(BaseModel $model, \Exception $exception)
	{
		if(env('APP_ENV') == 'local'){
			throw $exception;
		}
		\Notify::danger('Error : '.$exception->getMessage());
		return back();
	}

	/**
	 * @inheritDoc
	 */
	protected function afterUnchangedUpdate(BaseModel $model)
	{
		\Notify::info('No changes made to '.$model::friendlyName());
	}

	/**
	 * @inheritDoc
	 */
	protected function afterSuccessfullUpdate(BaseModel $model)
	{
		\Notify::success($model::friendlyName().' has been saved');
	}

}
