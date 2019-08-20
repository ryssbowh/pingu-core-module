<?php

namespace Pingu\Core\Traits\Controllers;

use Illuminate\Validation\Validator;
use Pingu\Core\Entities\BaseModel;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait UpdatesModel 
{
	/**
	 * Updates a model
	 * 
	 * @param  BaseModel
	 * @return response
	 */
	public function update(BaseModel $model)
	{	
		try{
			$model = $this->getUpdateModel($model);
			$this->beforeUpdate($model);
			$validated = $this->validateUpdateRequest($model);
			$changes = $this->performUpdate($model, $validated);
			if($changes){
				$this->afterSuccessfullUpdate($model);	
			}
			else{
				$this->afterUnchangedUpdate($model);
			}
		}
		catch(\Exception $e){
			$this->onUpdateFailure($model, $e);
		}

		return $this->onUpdateSuccess($model);
	}

	/**
	 * Gets the model used for updating
	 * 
	 * @param  BaseModel $model
	 * @return BaseModel
	 */
	protected function getUpdateModel(BaseModel $model)
	{
		return $model;
	}

	/**
	 * Do stuff before updating
	 * 
	 * @param  BaseModel $model
	 */
	protected function beforeUpdate(BaseModel $model){}

	/**
	 * Performs update
	 * 
	 * @param  BaseModel $model
	 * @param  array     $validated
	 */
	protected function performUpdate(BaseModel $model, array $validated)
	{
		return $model->saveWithRelations($validated);
	}

	/**
	 * Response when model can't be saved
	 * 
	 * @param  BaseModel $model
	 * @param  ModelNotSaved $exception 
	 */
	protected function onUpdateFailure(BaseModel $model, \Exception $exception){}

	/**
	 * do things after a successfull update
	 * 
	 * @param  BaseModel $model
	 */
	protected function afterSuccessfullUpdate(BaseModel $model){}

	/**
	 * do things after a successfull update that didn't change the model's attributes
	 * 
	 * @param  BaseModel $model
	 */
	protected function afterUnchangedUpdate(BaseModel $model){}

	/**
	 * Response after a successfull update
	 * 
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function onUpdateSuccess(BaseModel $model){}

	/**
	 * Validates a request and return validated array
	 * 
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function validateUpdateRequest(BaseModel $model)
	{
		$validator = $this->getUpdateValidator($model);
		$this->modifyUpdateValidator($validator);
		$validator->validate();
		$validated = $validator->validated();
		return $validated;
	}

	/**
	 * creates the validator for a store request
	 * 
	 * @return Validator
	 */
	protected function getUpdateValidator(BaseModel $model)
	{
		$fields = $this->getEditFields($model);
		return $model->makeValidator($this->request->all(), $fields);
	}

	/**
	 * Modify the store request validator
	 * 
	 * @param  Validator $validator
	 */
	protected function modifyUpdateValidator(Validator $validator){}
}
