<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Entities\BaseModel;

trait DeletesModel
{
	/**
	 * Deletes a model
	 */
	public function delete(BaseModel $model)
	{
		try{
			$this->beforeDeletion($model);
			$this->performDelete($model);
			$this->afterSuccessfullDeletion($model);
		}
		catch(\Exception $e){
			return $this->onDeletionFailure($model, $e);
		}

		return $this->onDeleteSuccess($model);
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
	 * Response when deletion fails
	 * 
	 * @param  BaseModel
	 * @param  \Exception $e
	 */
	protected function onDeletionFailure(BaseModel $model, \Exception $e){}

	/**
	 * Response when deletion succeeds
	 * 
	 * @param  BaseModel $model
	 */
	protected function onDeleteSuccess(BaseModel $model){}

}
