<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Entities\BaseModel;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait DeletesAjaxModel 
{
	/**
	 * Deletes a model
	 * @param  BaseModel $model
	 * @return array
	 */
	public function delete(BaseModel $model)
	{
		try{
			$this->beforeDestroying($model);
			$this->performDelete($model);
		}
		catch(\Exception $e){
			$this->afterDeletionFailure($model, $e);
			return $this->onDeletionFailure($model, $e);
		}
		$this->afterSuccessfullDeletion($model);
		return $this->onSuccessfullDeletion($model);
	}

	/**
	 * Performs the deletion
	 * 
	 * @param  BaseModel $model
	 */
	public function performDelete(BaseModel $model)
	{
		$model->delete();
	}

	/**
	 * Called before destroying a model
	 * @param  Request       $request
	 * @param  BaseModel $model
	 */
	protected function beforeDestroying(BaseModel $model){}

	/**
	 * Do stuff after a model is destroyed
	 * @return [type] [description]
	 */
	protected function afterSuccessfullDeletion(BaseModel $model){}

	/**
	 * returns data after successfull deletion
	 * @param  Request $request
	 * @return array
	 */
	protected function onSuccessfullDeletion(BaseModel $model)
	{
		return ['message' => $model::friendlyName().' has been deleted'];
	}

	/**
	 * Do stuff after a model deletion failure
	 * @param  Request   $request
	 * @param  BaseModel $model
	 */
	protected function afterDeletionFailure(BaseModel $model, \Exception $e){}

	/**
	 * returns data after deletion failure
	 * @param  Request $request
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function onDeletionFailure(BaseModel $model, \Exception $e)
	{
		if(env('APP_ENV') == 'local'){
			throw $e;
		}
		throw new HttpException(422, $model::friendlyName()." couldn't de deleted");
	}
}
