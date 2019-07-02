<?php

namespace Pingu\Core\Traits\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Exceptions\ModelRelationsNotSaved;
use Pingu\Forms\Support\Form;
use Pingu\Forms\Support\ModelForm;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait CreatesAjaxModel 
{
	/**
	 * Creates a model form and return it as a string
	 * 
	 * @return array
	 */
	public function create()
	{	
		$url = ['url' => $this->getStoreUri()];
		$form = new ModelForm($url, 'POST', $this->model);
		$form->addViewSuggestion('forms.modal')
			->addSubmit()
			->option('title', 'Add a '.$this->model::friendlyName());
		$this->afterStoreFormCreated($form);
		return ['form' => $form->renderAsString()];
	}

	/**
	 * Stores a model
	 * 
	 * @return array
	 */
	public function store()
	{
		try{
			$validated = $this->validateStoreRequest($this->model);
			$this->model->saveWithRelations($validated);
			$this->afterSuccessfullStore($this->model);
		}
		catch(ModelNotSaved $e){
			$this->onStoreFailure($this->model, $e);
		}
		catch(\Exception $e){
			$this->onStoreFailure($this->model, $e);
		}
		catch(ModelRelationsNotSaved $e){
			$this->onStoreRelationshipsFailure($this->model, $e);
		}

		return $this->onStoreSuccess($this->model);
	}

	/**
	 * Validates a request and return validated array
	 * 
	 * @param  BaseModel $model 
	 * @return array
	 */
	protected function validateStoreRequest(BaseModel $model)
	{
		return $model->validateForm($this->request->post(), $model->getAddFormFields(), false);
	}

	/**
	 * Callback when model can't be saved
	 * 
	 * @param  BaseModel $model
	 * @param  ModelNotSaved $exception 
	 */
	protected function onStoreFailure(BaseModel $model, \Exception $exception)
	{
		throw new HttpException(422, $exception->getMessage());
	}

	/**
	 * Callback when model's relationships can't be saved
	 * 
	 * @param  BaseModel $model
	 * @param  ModelRelationsNotSaved $exception 
	 */
	protected function onStoreRelationshipsFailure(BaseModel $model, ModelRelationsNotSaved $exception)
	{
		throw new HttpException(422, $exception->getMessage());
	}

	/**
	 * do things after a successfull store
	 * 
	 * @param  BaseModel $model
	 */
	protected function afterSuccessfullStore(BaseModel $model){}

	/**
	 * Returns data after a successfull store
	 * 
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function onStoreSuccess(BaseModel $model)
	{
		return ['model' => $model, 'message' => $model::friendlyName()." has been created"];
	}

	/**
	 * Returns the store uri for this model
	 * 
	 * @return string
	 */
	protected function getStoreUri()
	{
		return $this->getModel()::getAjaxUri('store');
	}

	/**
	 * Edit the store form
	 * 
	 * @param  Form $form
	 */
	protected function afterStoreFormCreated(Form $form){}
}
