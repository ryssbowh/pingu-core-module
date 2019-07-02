<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Forms\ConfirmDeletion;

trait DeletesAdminModel
{
	/**
	 * Deletes a model
	 */
	public function delete(BaseModel $model)
	{
		$this->beforeDeletion($model);
		try{
			$this->performDelete($model);
			$this->afterSuccessfullDeletion($model);
		}
		catch(\Exception $e){
			return $this->onDeletionFailure($model, $e);
		}

		return $this->onSuccessfullDeletion($model);
	}

	/**
	 * Confirm deletion requests
	 * 
	 * @param  BaseModel $model
	 * @return response
	 */
	public function confirmDelete(BaseModel $model)
	{
		$form = new ConfirmDeletion($model);
		return view('pages.deleteModel')->with(['form' => $form, 'model' => $model]);
	}

	/**
	 * Actions to do before mdoel is deleted
	 * 
	 * @param  BaseModel $model
	 */
	public function beforeDeletion(BaseModel $model){}

	/**
	 * Actions to do after the model is deleted
	 * 
	 * @param  BaseModel $model
	 */
	public function afterSuccessfullDeletion(BaseModel $model){}

	/**
	 * Perform the actual deletion
	 * 
	 * @param  BaseModel $model
	 */
	protected function performDelete(BaseModel $model)
	{
		$model->delete();
	}

	/**
	 * Action when deletion fails
	 * 
	 * @param  BaseModel
	 * @param  \Exception $e
	 */
	protected function onDeletionFailure(BaseModel $model, \Exception $e)
	{
		if(env('APP_ENV') == 'local'){
			throw $e;
		}
		\Notify::danger('Error : '.$e->getMessage());
		exit();
		return back();
	}

	/**
	 * Action when deletion succeeds
	 * 
	 * @param  BaseModel $model
	 */
	protected function onSuccessfullDeletion(BaseModel $model)
	{
		\Notify::success($model::friendlyName().' has been deleted');
		return redirect($model::getAdminUri('index', true));
	}

}
