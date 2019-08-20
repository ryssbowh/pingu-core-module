<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Support\Form;
use Pingu\Forms\Support\ModelForm;

trait CreatesModel
{
	/**
	 * Create form for a model. Model must be set within the route
	 * 
	 * @return view
	 */
	public function create(...$parameters)
	{	
		$this->routeParameters = $parameters;
		
		$this->beforeCreate();
		$form = $this->getCreateForm();
		
		return $this->onCreateFormCreated($form);
	}

	/**
	 * Callback before create request
	 */
	protected function beforeCreate(){}

	/**
	 * Builds the form for a create request
	 * 
	 * @param  string  $model
	 * @return ModelForm
	 */
	protected function getCreateForm()
	{
		$model = $this->getCreateModel();
		$model = new $model;
		$url = $this->getStoreUri();
		if(!is_array($url)){
			$url = ['url' => $url];
		}
		$fields = $this->getCreateFields($model);

		$form = new ModelForm($url, 'POST', $model, false, $fields);
		$form->addSubmit('Submit');

		$this->afterCreateFormCreated($form);

		return $form;
	}

	/**
	 * Do stuff after the form is created
	 * 
	 * @param  Form   $form
	 */
	protected function afterCreateFormCreated(Form $form){}

	/**
	 * get the fields to be created
	 * 
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function getCreateFields(BaseModel $model)
	{
		return $model->getAddFormFields();
	}

	/**
	 * Get the model being created
	 * 
	 * @return string
	 */
	protected function getCreateModel()
	{
		return $this->model;
	}

	/**
	 * Callback after the create form is created
	 * 
	 * @param  Form   $form
	 */
	protected function onCreateFormCreated(Form $form){}

	/**
	 * Get the url for a store request
	 * 
	 * @return string|array
	 */
	protected function getStoreUri()
	{
		return ['url' => $this->model::makeUri('store', [], $this->getStoreUriPrefix())];
	}

	/**
	 * Prefix the store uri
	 * 
	 * @return string
	 */
	protected function getStoreUriPrefix()
	{
		return '';
	}

}
