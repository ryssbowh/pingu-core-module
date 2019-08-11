<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Support\Form;
use Pingu\Forms\Support\ModelForm;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait EditsAjaxModel 
{
	/**
	 * Edits a model, builds a form and send it as string
	 * 
	 * @param  BaseModel $model
	 * @return array
	 */
	public function edit(BaseModel $model): array
	{
		$url = ['url' => $this->getUpdateUri($model)];
		$form = new ModelForm($url, 'PUT', $model);
		$form->addViewSuggestion('forms.modal')
			->addSubmit()
			->option('title', 'Edit a '.$model::friendlyName());
		$this->afterUpdateFormCreated($form);
		return ['form' => $form->renderAsString()];
	}

	/**
	 * Updates a model
	 * 
	 * @param  BaseModel
	 * @return response
	 */
	public function update(BaseModel $model): array
	{	
		try{
			$validated = $this->validateUpdateRequest($model);
			$this->performUpdate($model, $validated);
			$this->afterSuccessfullUpdate($model);
		}
		catch(ModelNotSaved $e){
			$this->onUpdateFailure($model, $e);
		}
		catch(ModelRelationsNotSaved $e){
			$this->onUpdateRelationshipsFailure($model, $e);
		}

		return $this->onSuccessfullUpdate($model);
	}

	protected function performUpdate(BaseModel $model, array $validated)
	{
		$model->saveWithRelations($validated);
	}

	/**
	 * Gets the update uri
	 * @return string
	 */
	protected function getUpdateUri(BaseModel $model)
	{
		return $model::transformUri('update', $model, config('core.ajaxPrefix'));
	}

	/**
	 * Modify an update form
	 * @param  Form $form
	 */
	protected function afterUpdateFormCreated(Form $form){}

	/**
	 * Callback when model can't be saved
	 * @param  BaseModel $model
	 * @param  ModelNotSaved $exception 
	 */
	protected function onUpdateFailure(BaseModel $model, \Exception $exception)
	{
		throw new HttpException(422, $exception->getMessage());
	}

	/**
	 * Callback when model's relationships can't be saved
	 * @param  BaseModel $model
	 * @param  ModelRelationsNotSaved $exception 
	 */
	protected function onUpdateRelationshipsFailure(BaseModel $model, ModelRelationsNotSaved $exception)
	{
		throw new HttpException(422, $exception->getMessage());
	}

	/**
	 * do things after a successfull update
	 * @param  BaseModel $model
	 */
	protected function afterSuccessfullUpdate(BaseModel $model){}

	/**
	 * Returns data after a successfull update
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function onSuccessfullUpdate(BaseModel $model)
	{
		return ['model' => $model, 'message' => $model::friendlyname().' has been updated'];
	}

	/**
	 * Vaildates an update request and returns validated data
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function validateUpdateRequest(BaseModel $model)
	{
		return $model->validateForm($this->request->post(), $model->getEditFormFields(), true);
	}
}
