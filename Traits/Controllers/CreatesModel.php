<?php

namespace Pingu\Core\Traits\Controllers;

use ContextualLinks,Notify;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Pingu\Core\Contracts\HasContextualLinks;
use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Support\ModelForm;

trait CreatesModel
{
	/**
	 * Create form for a model. Model must be set within the route
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
	 * @return redirect
	 */
	public function store()
	{
		$this->beforeStore();

		$modelStr = $this->getStoreModel();
		$model = new $modelStr;
		$validated = $this->validateStoreRequest($modelStr);

		try{
			$this->performStore($model, $validated);
			$this->onModelCreated($model);
			$this->afterSuccessfullStore($model);
		}
		catch(ModelNotSaved $e){
			$this->onStoreFailure($model, $e);
		}
		catch(ModelRelationsNotSaved $e){
			$this->onStoreRelationshipsFailure($model, $e);
		}

		return $this->onSuccessfullStore($model);
	}

	/**
	 * Store the model
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
	 * @return string
	 */
	protected function getStoreModel()
	{
		return $this->getModel();
	}

	/**
	 * Get the view for a create request
	 * @param  ModelForm $form
	 * @param  string $model
	 * @return view
	 */
	protected function getCreateView(ModelForm $form)
	{
		$with = [
			'form' => $form,
			'model' => $this->getModel(),
		];
		$this->addVariablesToCreateView($with);
		return view($this->getCreateViewName())->with($with);
	}

	/**
	 * View name for creating models
	 * @return string
	 */
	protected function getCreateViewName()
	{
		return 'pages.addModel';
	}

	/**
	 * Callback to add variables to the view
	 * @param array &$with
	 */
	protected function addVariablesToCreateView(array &$with){}

	/**
	 * Builds the form for a create request
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
		$fields = $this->getCreateFields($model);

		$form = new ModelForm($url, 'POST', new $model, $fields);
		$form->addSubmit('Submit');

		return $form;
	}

	/**
	 * Modify the create form
	 * @param  ModelForm $form
	 */
	protected function modifyCreateForm(ModelForm $form){}

	/**
	 * Get the url for a store request
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
	 * @return mixed
	 */
	protected function onSuccessfullStore(BaseModel $model)
	{
		return back();
	}

	/**
	 * Validates a request and return validated array
	 * @param  string $model 
	 * @return array
	 */
	protected function validateStoreRequest(string $model)
	{
		$validator = $this->getStoreValidator($model);
		$this->modifyStoreValidator($validator);
		$validator->validate();
		return $validator->validated();
	}

	/**
	 * makes the validator for a store request
	 * @return Validator
	 */
	protected function getStoreValidator(string $model)
	{
		$fields = $this->getCreateFields($model);
		return (new $model)->makeValidator($this->request->post(), $fields);
	}

	/**
	 * Gets the list of fields for a create request
	 * @param  string $model
	 * @return array
	 */
	protected function getCreateFields(string $model)
	{
		return (new $model)->getAddFormFields();
	}

	/**
	 * Modify the store request validator
	 * @param  Validator $validator
	 */
	protected function modifyStoreValidator(Validator $validator){}

	/**
	 * Callback after model is sucessfully added
	 * @param  BaseModel $model
	 */
	protected function onModelCreated(BaseModel $model)
	{
		Notify::success($model::friendlyName().' has been saved');
	}

	/**
	 * Do stuff after a model has been stored
	 * @param  BaseModel $model
	 */
	protected function afterSuccessfullStore(BaseModel $model){}

	/**
	 * Callback when model can't be saved
	 * @param  BaseModel $model
	 * @param  ModelNotSaved $exception 
	 */
	protected function onStoreFailure(BaseModel $model, ModelNotSaved $exception)
	{
		Notify::info('Error while saving '.$model::friendlyName());
	}

	/**
	 * Callback when model's relationships can't be saved
	 * @param  BaseModel $model
	 * @param  ModelRelationsNotSaved $exception 
	 */
	protected function onStoreRelationshipsFailure(BaseModel $model, ModelRelationsNotSaved $exception)
	{
		Notify::info($model::friendlyName().' was partially saved, check manually');
	}

}
