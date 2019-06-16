<?php

namespace Pingu\Core\Traits\Controllers;

use ContextualLinks,Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Support\Form;
use Pingu\Forms\Support\ModelForm;
use Pingu\Forms\Support\Type;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait HandlesAjaxModel 
{
	/**
	 * @inheritDoc
	 */
	public function index(Request $request): array
	{
		$modelStr = $this->getModel();
		$model = new $modelStr;

		$filters = $request->input('filters', []);
		$options = $request->input('options', []);
		$pageIndex = $request->input('pageIndex', 1);
		$pageSize = $request->input('pageSize', $model->getPerPage());
		$sortField = $request->input('sortField', $model->getKeyName());
		$sortOrder = $request->input('sortOrder', 'asc');

		$fieldsDef = $model->getFieldDefinitions();
		$query = $model->newQuery();
		foreach($filters as $field => $value){
			if(!isset($fieldsDef[$field])){
				continue;
			}
			$fieldDef = $fieldsDef[$field];
			
			if(!is_null($value)){
				$fieldDef->option('type')->filterQueryModifier($query, $field, $value);
			}
		}

		$count = $query->count();

		if($sortField){
			$query->orderBy($sortField, $sortOrder);
		}

		$query->offset(($pageIndex-1) * $pageSize)->take($pageSize);

		$models = $query->get();

		return ['models' => $models->toArray(), 'total' => $count];
	}

	/**
	 * @inheritDoc
	 */
	public function edit(Request $request, BaseModel $model): array
	{
		$url = ['url' => $this->getUpdateUri($request)];
		$form = new ModelForm($url, 'PUT', $model);
		$form->addViewSuggestion('forms.modal')
			->addSubmit()
			->option('title', 'Edit a '.$model::friendlyName());
		$this->afterUpdateFormCreated($request, $form);
		return ['form' => $form->renderAsString()];
	}

	/**
	 * @inheritDoc
	 */
	public function update(Request $request, BaseModel $model): array
	{	
		$validated = $this->validateUpdateRequest($request, $model);

		try{
			$model->saveWithRelations($validated);
			$this->afterSuccessfullUpdate($request, $model);
		}
		catch(ModelNotSaved $e){
			$this->onUpdateFailure($request, $model, $e);
		}
		catch(ModelRelationsNotSaved $e){
			$this->onUpdateRelationshipsFailure($request, $model, $e);
		}

		return $this->onSuccessfullUpdate($request, $model);
	}

	/**
	 * @inheritDoc
	 */
	public function destroy(Request $request, BaseModel $model): array
	{
		$this->onDestroying($request, $model);
		if($model->delete()){
			$this->afterSuccessfullDeletion($request, $model);
			return $this->onSuccessfullDeletion($request, $model);
		}
		else{
			$this->afterDeletionFailure($request, $model);
			return $this->onDeletionFailure($request, $model);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function create(Request $request): array
	{	
		$model = $this->getModel();
		$url = ['url' => $this->getStoreUri($request)];
		$form = new ModelForm($url, 'POST', new $model);
		$form->addViewSuggestion('forms.modal')
			->addSubmit()
			->option('title', 'Add a '.$model::friendlyName());
		$this->afterStoreFormCreated($request, $form);
		return ['form' => $form->renderAsString()];
	}

	/**
	 * @inheritDoc
	 */
	public function store(Request $request): array
	{
		$modelStr = $this->getModel();
		$model = new $modelStr;

		$validated = $this->validateStoreRequest($request, $model);

		try{
			$model->saveWithRelations($validated);
			$this->afterSuccessfullStore($request, $model);
		}
		catch(ModelNotSaved $e){
			$this->onStoreFailure($request, $model, $e);
		}
		catch(ModelRelationsNotSaved $e){
			$this->onStoreRelationshipsFailure($request, $model, $e);
		}

		return $this->onStoreSuccess($request, $model);
	}

	/**
	 * @inheritDoc
	 */
	public function get(Request $request, BaseModel $model): BaseModel
	{
		return $model;
	}

	public function patch(Request $request): array
	{
		$post = $request->post();
		if(!isset($post['models'])){
			throw new HttpException(422, "'models' must be set for a patch request");
		}
		$model = $this->getModel();
		$model = new $model;
		$models = collect();
		foreach($post['models'] as $data){
			if(!isset($data[$model->getKeyName()])){
				throw new HttpException(422, "The primary key is not set for ".$model::friendlyName());
			}
			$item = $this->getModel()::findOrFail($data[$model->getKeyName()]);
			unset($data[$model->getKeyName()]);
			$validated = $item->validateForm($data, array_keys($data));
			$item->saveWithRelations($validated);
			$models[] = $item->refresh();
		}
		$this->afterSuccessfullPatch($request, $models);
		return $this->onSuccessfullPatch($request, $models);
	}

	/**
	 * Gets the update uri
	 * @param  Request $request
	 * @return string
	 */
	protected function getUpdateUri(Request $request)
	{
		return rtrim($request->path(), '/edit');
	}

	/**
	 * Modify an update form
	 * @param  Request $request
	 * @param  Form $form
	 */
	protected function afterUpdateFormCreated(Request $request, Form $form){}

	/**
	 * Callback when model can't be saved
	 * @param  Request   $request
	 * @param  BaseModel $model
	 * @param  ModelNotSaved $exception 
	 */
	protected function onUpdateFailure(Request $request, BaseModel $model, ModelNotSaved $exception)
	{
		throw new HttpException(422, $exception->getMessage());
	}

	/**
	 * Callback when model's relationships can't be saved
	 * @param  Request   $request
	 * @param  BaseModel $model
	 * @param  ModelRelationsNotSaved $exception 
	 */
	protected function onUpdateRelationshipsFailure(Request $request, BaseModel $model, ModelRelationsNotSaved $exception)
	{
		throw new HttpException(422, $exception->getMessage());
	}

	/**
	 * do things after a successfull update
	 * @param  Request       $request
	 * @param  BaseModel $model
	 */
	protected function afterSuccessfullUpdate(Request $request, BaseModel $model){}

	/**
	 * Returns data after a successfull update
	 * @param  Request       $request
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function onSuccessfullUpdate(Request $request, BaseModel $model)
	{
		return ['model' => $model, 'message' => $model::friendlyname().' has been updated'];
	}

	/**
	 * Vaildates an update request and returns validated data
	 * @param  Request       $request
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function validateUpdateRequest(Request $request, BaseModel $model)
	{
		return $model->validateForm($request->post(), $model->getEditFormFields());
	}

	/**
	 * Called before destroying a model
	 * @param  Request       $request
	 * @param  BaseModel $model
	 */
	protected function onDestroying(Request $request, BaseModel $model)
	{}

	/**
	 * Do stuff after a model is destroyed
	 * @return [type] [description]
	 */
	protected function afterSuccessfullDeletion(){}

	/**
	 * returns data after successfull deletion
	 * @param  Request $request
	 * @return array
	 */
	protected function onSuccessfullDeletion(Request $request)
	{
		return ['message' => $this->getModel()::friendlyName().' has been deleted'];
	}

	/**
	 * Do stuff after a model deletion failure
	 * @param  Request   $request
	 * @param  BaseModel $model
	 */
	protected function afterDeletionFailure(Request $request, BaseModel $model){}

	/**
	 * returns data after deletion failure
	 * @param  Request $request
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function onDeletionFailure(Request $request, BaseModel $model)
	{
		throw new HttpException(422, $model::friendlyName()." couldn't de deleted");
	}

	/**
	 * Validates a request and return validated array
	 * @param  Request   $request
	 * @param  BaseModel $model 
	 * @return array
	 */
	protected function validateStoreRequest(Request $request, BaseModel $model)
	{
		return $model->validateForm($request->post(), $model->getAddFormFields());
	}

	/**
	 * Callback when model can't be saved
	 * @param  Request   $request
	 * @param  BaseModel $model
	 * @param  ModelNotSaved $exception 
	 */
	protected function onStoreFailure(Request $request, BaseModel $model, ModelNotSaved $exception)
	{
		throw new HttpException(422, $exception->getMessage());
	}

	/**
	 * Callback when model's relationships can't be saved
	 * @param  Request   $request
	 * @param  BaseModel $model
	 * @param  ModelRelationsNotSaved $exception 
	 */
	protected function onStoreRelationshipsFailure(Request $request, BaseModel $model, ModelRelationsNotSaved $exception)
	{
		throw new HttpException(422, $exception->getMessage());
	}

	/**
	 * do things after a successfull store
	 * @param  Request       $request
	 * @param  BaseModel $model
	 */
	protected function afterSuccessfullStore(Request $request, BaseModel $model){}

	/**
	 * Returns data after a successfull store
	 * @param  Request       $request
	 * @param  BaseModel $model
	 * @return array
	 */
	protected function onStoreSuccess(Request $request, BaseModel $model)
	{
		return ['model' => $model, 'message' => $model::friendlyName()." has been created"];
	}

	/**
	 * @inheritDoc
	 */
	protected function getStoreUri(Request $request): string
	{
		$model = $this->getModel();
		return $model::getAjaxUri('store');
	}

	/**
	 * Edit the store form here
	 * @param  Request $request
	 * @param  Form $form
	 */
	protected function afterStoreFormCreated(Request $request, Form $form){}

	/**
	 * Returns data after a successfull patch
	 * @param  Request    $request
	 * @param  Collection $models
	 * @return array
	 */
	protected function onSuccessfullPatch(Request $request, Collection $models): array
	{
		return ['message' => str_plural($this->getModel()::friendlyName())." have been updated", 'models' => $models];
	}

	/**
	 * Do stuff after successfull patch
	 * @param  Request    $request
	 * @param  Collection $models 
	 */
	protected function afterSuccessfullPatch(Request $request, Collection $models){}
}
