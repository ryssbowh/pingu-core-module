<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Support\Form;
use Pingu\Forms\Support\ModelForm;

trait EditsModel 
{
	/**
	 * Edits a model, builds a form and send it as string
	 * 
	 * @param  BaseModel $model
	 * @return array
	 */
	public function edit(BaseModel $model)
	{
		$this->beforeEdit($model);
		$form = $this->getEditForm($model);

		return $this->onEditFormCreated($form, $model);
	}

	/**
	 * Builds the form for an edit request
	 * @param  BaseModel $model 
	 * @return FormModel
	 */
	protected function getEditForm(BaseModel $model)
	{
		$model = $this->getEditModel($model);
		$url = $this->getUpdateUri($model);
		if(!is_array($url)){
			$url = ['url' => $url];
		}
		$fields = $this->getEditFields($model);

		$form = new ModelForm($url, 'PUT', $model, true);
		$form->addSubmit('Submit');

		$this->afterEditFormCreated($form, $model);

		return $form;
	}

	/**
	 * Get the model being edited
	 * 
	 * @return BaseModel
	 */
	protected function getEditModel(BaseModel $model)
    {
        return $model;
    }

	/**
	 * Callback before edit request
	 *
	 * @param BaseModel $model
	 */
	protected function beforeEdit(BaseModel $model){}

	/**
	 * Gets the update uri
	 * 
	 * @return string
	 */
	protected function getUpdateUri(BaseModel $model)
	{
		return $model::makeUri('update', $model, $this->getUpdateUriPrefix());
	}

	/**
	 * get the fields to be edited
	 * 
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function getEditFields(BaseModel $model)
	{
		return $model->getEditFormFields();
	}

	/**
	 * Prefix the update uri
	 * 
	 * @return string
	 */
	protected function getUpdateUriPrefix()
	{
		return '';
	}

	/**
	 * Modify the edit form
	 * 
	 * @param  Form $form
	 */
	protected function afterEditFormCreated(Form $form, BaseModel $model){}

	/**
	 * Response to client
	 * 
	 * @return mixed
	 */
	protected function onEditFormCreated(){}

}
