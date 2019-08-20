<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Forms\ConfirmDeletion;

trait DeletesAdminModel
{
	use DeletesModel;

	/**
	 * Confirm deletion requests
	 * 
	 * @param  BaseModel $model
	 * @return view
	 */
	public function confirmDelete(BaseModel $model)
	{
		$form = new ConfirmDeletion($model);
		$with = [
			'form' => $form, 
			'model' => $model
		];
		$this->addVariablesToDeleteView($with);
		return view('pages.deleteModel')->with($with);
	}

	protected function addVariablesToDeleteView(&$with){}

	/**
	 * @inheritDoc
	 */
	protected function onDeleteFailure(BaseModel $model, \Exception $e)
	{
		if(env('APP_ENV') == 'local'){
			throw $e;
		}
		\Notify::danger('Error : '.$e->getMessage());
		return back();
	}

	/**
	 * @inheritDoc
	 */
	protected function onDeleteSuccess(BaseModel $model)
	{
		return redirect($model::makeUri('index', $model, adminPrefix()));
	}

	/**
	 * @inheritDoc
	 */
	protected function afterSuccessfullDeletion(BaseModel $model){
		\Notify::success($model::friendlyName().' has been deleted');
	}

}
