<?php

namespace Pingu\Core\Traits\Controllers;

use Illuminate\Validation\Validator;
use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Support\Form;
use Pingu\Forms\Support\ModelForm;

trait CreatesAdminModel
{
	/**
	 * Create form for a model. Model must be set within the route
	 * 
	 * @return view
	 */
	public function create()
	{
		$this->beforeCreate();
		$form = $this->getCreateForm();
		$this->modifyCreateForm($form);

		return $this->getCreateView($form);
	}

	/**
	 * Stores a new model, model must be set within the route
	 * 
	 * @return redirect
	 */
	public function store()
	{
		$this->beforeStore();

		$modelStr = $this->getStoreModel();
		$model = new $modelStr;

		try{
			$validated = $this->validateStoreRequest($model);
			$this->performStore($model, $validated);
			$this->onModelCreated($model);
			$this->afterSuccessfullStore($model);
		}
		catch(\Exception $e){
			return $this->onStoreFailure($model, $e);
		}

		return $this->onSuccessfullStore($model);
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
	 * Callback before create request
	 */
	protected function beforeCreate(){}

	/**
	 * Callback before store request
	 */
	protected function beforeStore(){}

	/**
	 * Gets the model being stored
	 * 
	 * @return string
	 */
	protected function getStoreModel()
	{
		return $this->model;
	}

	/**
	 * Get the view for a create request
	 * 
	 * @param  Form $form
	 * @param  string $model
	 * @return view
	 */
	protected function getCreateView(Form $form)
	{
		$with = [
			'form' => $form,
			'model' => $this->model,
		];
		$this->addVariablesToCreateView($with);
		return view($this->getCreateViewName())->with($with);
	}

	/**
	 * View name for creating models
	 * 
	 * @return string
	 */
	protected function getCreateViewName()
	{
		return 'pages.addModel';
	}

	/**
	 * Callback to add variables to the view
	 * 
	 * @param array &$with
	 */
	protected function addVariablesToCreateView(array &$with){}

	/**
	 * Builds the form for a create request
	 * 
	 * @param  string  $model
	 * @return ModelForm
	 */
	protected function getCreateForm()
	{
		
		$url = $this->getStoreUrl();
		if(!is_array($url)){
			$url = ['url' => $url];
		}
		$model = $this->getStoreModel();
		$model = new $model;
		$fields = $this->getCreateFields($model);

		$form = new ModelForm($url, 'POST', $model, $fields);
		$form->addSubmit('Submit');

		return $form;
	}

	/**
	 * Modify the create form
	 * @param  Form $form
	 */
	protected function modifyCreateForm(Form $form){}

	/**
	 * Get the url for a store request
	 * 
	 * @return string
	 */
	protected function getStoreUrl()
	{
		$segments = request()->segments();
		array_pop($segments);
		return ['url' => '/'.implode($segments,'/')];
	}

	/**
	 * Callback after store action, this is where you redirect users.
	 * 
	 * @return mixed
	 */
	protected function onSuccessfullStore(BaseModel $model)
	{
		return back();
	}

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
		$validated = $this->uploadFiles($validated, $model);
		return $validated;
	}

	/**
	 * makes the validator for a store request
	 * 
	 * @return Validator
	 */
	protected function getStoreValidator(BaseModel $model)
	{
		$fields = $this->getCreateFields($model);
		return $model->makeValidator($this->request->all(), $fields, false);
	}

	/**
	 * Gets the list of fields for a create request
	 * 
	 * @param  string $model
	 * @return array
	 */
	protected function getCreateFields(BaseModel $model)
	{
		return $model->getAddFormFields();
	}

	/**
	 * Modify the store request validator
	 * 
	 * @param  Validator $validator
	 */
	protected function modifyStoreValidator(Validator $validator){}

	/**
	 * Callback after model is sucessfully added
	 * 
	 * @param  BaseModel $model
	 */
	protected function onModelCreated(BaseModel $model)
	{
		\Notify::success($model::friendlyName().' has been saved');
	}

	/**
	 * Do stuff after a model has been stored
	 * 
	 * @param  BaseModel $model
	 */
	protected function afterSuccessfullStore(BaseModel $model){}

	/**
	 * Callback when an exception is caught
	 * 
	 * @param  BaseModel $model
	 * @param  ModelNotSaved $exception 
	 */
	protected function onStoreFailure(BaseModel $model, $exception)
	{
		if(env('APP_ENV') == 'local'){
			throw $exception;
		}
		\Notify::danger('Error : '.$exception->getMessage());
		return back();
	}

	/**
	 * Uploads file submitted in post and populate validated array with a Media object
	 * 
	 * @param  array     $validated
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function uploadFiles(array $validated, BaseModel $model)
	{
		$toUpload = array_intersect_key($validated, $this->request->allFiles());
		foreach($toUpload as $name => $file){
			$media = $model->uploadFormFile($file, $name);
			$validated[$name] = $media;
		}
		return $validated;
	}

}
