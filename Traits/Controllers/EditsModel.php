<?php

namespace Pingu\Core\Traits\Controllers;

use ContextualLinks,Notify;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Pingu\Core\Contracts\Models\HasContextualLinksContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Support\Form;
use Pingu\Forms\Support\ModelForm;

trait EditsModel
{
	/**
	 * Edit a model
	 * @param  BaseModel $model
	 * @return view
	 */
	public function edit(BaseModel $model)
	{	
		$this->beforeEdit($model);
		$model = $this->getEditModel($model);
		$this->addContextualLinks($model);
		$form = $this->getEditForm($model);
		return $this->getEditView($form, $model);
	}

	/**
	 * Updates a model
	 * @param  BaseModel $model
	 * @return mixed
	 */
	public function update(BaseModel $model)
	{
		$this->beforeUpdate($model);

		$model = $this->getEditModel($model);
		$validated = $this->validateUpdateRequest($model);

		try{
			$changes = $this->performUpdate($model, $validated);
			if($changes){
				$this->onModelUpdatedWithChanges($model);
			}
			else{
				$this->onModelUpdatedWithoutChanges($model);
			}
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

	/**
	 * Performs the actual update to the model
	 * @param  BaseModel $model
	 * @param  array     $validated
	 * @return bool
	 */
	protected function performUpdate(BaseModel $model, array $validated)
	{
		return $model->saveWithRelations($validated);
	}

	/**
	 * Called before editing
	 * @param  BaseModel $model
	 */
	protected function beforeEdit(BaseModel $model){}

	/**
	 * Called before updating
	 * @param  BaseModel $model
	 */
	protected function beforeUpdate(BaseModel $model){}

	/**
	 * Modify the edit form
	 * @param  FormModel $form
	 */
	protected function modifyEditForm(Form $form, BaseModel $model){}

	/**
	 * Gets the model being edited
	 * @param  BaseModel $model
	 * @return BaseModel
	 */
	protected function getEditModel(BaseModel $model)
	{
		return $model;
	}

	/**
	 * Return the view for an edit request
	 * @param  FormModel      $form
	 * @param  BaseModel $model 
	 * @return view
	 */
	protected function getEditView(Form $form, BaseModel $model)
	{
		$with = [
			'form' => $form,
			'model' => $model,
		];
		$this->addVariablesToEditView($with, $model);
		return view($this->getEditViewName())->with($with);
	}

	/**
	 * View name for editing models
	 * @return string
	 */
	protected function getEditViewName()
	{
		return 'pages.editModel';
	}

	/**
	 * Adds variables to the edit view
	 * @param array     &$with
	 * @param Basemodel $model
	 */
	protected function addVariablesToEditView(array &$with, Basemodel $model){}

	/**
	 * Builds the form for an edit request
	 * @param  BaseModel $model 
	 * @return FormModel
	 */
	protected function getEditForm(BaseModel $model)
	{
		$url = $this->getUpdateUrl($model);
		$form = new ModelForm(['url' => $url], 'PUT', $model);
		$form->addSubmit('Submit');
		$this->modifyEditForm($form, $model);
		return $form;
	}

	/**
	 * Get the url for an update request
	 * @param  BaseModel $model
	 * @return string
	 */
	protected function getUpdateUrl(BaseModel $model)
	{
		return $model::transformAdminUri('update', [$model], true);
	}

	/**
	 * Add contextual links for edit requests
	 * @param BaseModel $model
	 */
	protected function addContextualLinks(BaseModel $model)
	{
		if($model instanceof HasContextualLinksContract) ContextualLinks::addModelLinks($model);
	}

	/**
	 * Do things after a successfull update
	 * @param  BaseModel $model
	 */
	protected function afterSuccessfullUpdate(BaseModel $model){}

	/**
	 * Callback after store action, this is where you redirect users.
	 * @return mixed
	 */
	protected function onSuccessfullUpdate(BaseModel $model)
	{
		return back();
	}

	/**
	 * Callback when model can't be saved
	 * @param  BaseModel $model
	 * @param  ModelNotSaved $exception 
	 */
	protected function onUpdateFailure(BaseModel $model, ModelNotSaved $exception)
	{
		Notify::error('Error while saving '.$model::friendlyName());
	}

	/**
	 * Callback when model's relationships can't be saved
	 * @param  BaseModel $model
	 * @param  ModelRelationsNotSaved $exception 
	 */
	protected function onUpdateRelationshipsFailure(BaseModel $model, ModelRelationsNotSaved $exception)
	{
		Notify::error($model::friendlyName().' was partially saved, check manually');
	}

	/**
	 * Validates a request and return validated array
	 * @param  BaseModel $model 
	 * @return array
	 */
	protected function validateUpdateRequest(BaseModel $model)
	{
		$validator = $this->getUpdateValidator($model);
		$this->modifyUpdateValidator($validator, $model);
		$validator->validate();
		return $validator->validated();
	}

	/**
	 * Builds the validator for that model
	 * @param  BaseModel $model
	 * @return Validator
	 */
	protected function getUpdateValidator(BaseModel $model)
	{
		$fields = $this->getEditFields($model);
		return $model->makeValidator($this->request->all(), $fields);
	}

	/**
	 * Returns the fields being edited for that model
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function getEditFields(BaseModel $model)
	{
		return $model->getEditFormFields();
	}

	/**
	 * modifies the update validator
	 * @param  Validator $validator
	 */
	protected function modifyUpdateValidator(Validator $validator, BaseModel $model){}

	/**
	 * Callback when a model is saved without changes
	 * @param  BaseModel $model
	 */
	protected function onModelUpdatedWithoutChanges(BaseModel $model)
	{
		Notify::info('No changes made to '.$model::friendlyName());
	}

	/**
	 * Callback when a model is saved with changes
	 * @param  BaseModel $model
	 */
	protected function onModelUpdatedWithChanges(BaseModel $model)
	{
		Notify::success($model::friendlyName().' has been saved');
	}

}
