<?php

namespace Pingu\Core\Traits\Controllers;

use Illuminate\Validation\Validator;
use Pingu\Core\Entities\BaseModel;

trait StoresModel
{
	/**
	 * Stores a new model, model must be set within the route
	 * 
	 * @return redirect
	 */
	public function store()
	{
		try{
			$this->beforeStore();
			$modelStr = $this->getCreateModel();
			$model = new $modelStr;
			$validated = $this->validateStoreRequest($model);
			// dd($validated);
			$this->performStore($model, $validated);
			$this->afterStoreSuccess($model);
		}
		catch(\Exception $e){
			return $this->onStoreFailure($model, $e);
		}

		return $this->onStoreSuccess($model);
	}

	/**
	 * Store the model
	 * 
	 * @param  BaseModel $model
	 * @param  array     $validated
	 */
	protected function performStore(BaseModel $model, array $validated)
	{
		$model->saveWithRelations($validated);
	}

	/**
	 * Callback before store request
	 */
	protected function beforeStore(){}

	/**
	 * Validates a request and return validated array
	 * 
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function validateStoreRequest(BaseModel $model)
	{
		$validator = $this->getStoreValidator($model);
		$this->modifyStoreValidator($validator);
		$validator->validate();
		$validated = $validator->validated();
		return $validated;
	}

	/**
	 * creates the validator for a store request
	 * 
	 * @return Validator
	 */
	protected function getStoreValidator(BaseModel $model)
	{
		$fields = $this->getCreateFields($model);
		return $model->makeValidator($this->request->all(), $fields);
	}

	/**
	 * Modify the store request validator
	 * 
	 * @param  Validator $validator
	 */
	protected function modifyStoreValidator(Validator $validator){}

	/**
	 * Do stuff after a model has been stored
	 * 
	 * @param  BaseModel $model
	 */
	protected function afterStoreSuccess(BaseModel $model){}

	/**
	 * Returns response when store fails
	 * 
	 * @param  BaseModel  $model
	 * @param  \Exception $exception
	 * @return mixed
	 */
	protected function onStoreFailure(BaseModel $model, \Exception $exception){
		throw $exception;
	}

	/**
	 * Returns reponse when store succeeds
	 * 
	 * @param  BaseModel $model
	 * @return mixed
	 */
	protected function onStoreSuccess(BaseModel $model){}

}
