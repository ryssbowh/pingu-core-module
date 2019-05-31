<?php

namespace Pingu\Core\Traits;

use ContextualLinks,Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Form;
use Pingu\Forms\FormModel;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait AjaxModelController 
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

		$fieldsDef = $model->fieldDefinitions();
		$query = $model->newQuery();
		foreach($filters as $field => $value){
			if(!isset($fieldsDef[$field])){
				throw new HttpException(422, "field $field is not defined");
			}
			$fieldDef = $fieldsDef[$field];
			if(!is_null($value)){
				$fieldDef['type']::filterQueryModifier($query, $field, $value);
			}
		}

		if(isset($options['relatedModel'])){
			$relatedModel = $options['relatedModel']::find($options['relatedId']);
			$method = 'relatedJsGrid'.class_basename($options['relatedModel']);
			$model::$method($query, $relatedModel);
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
		$form = new FormModel(
			['url' => $this->getUpdateUri($request), 'method' => 'PUT'], 
			['submit' => ['Save'], 'view' => 'forms.modal', 'title' => 'Edit a '.$model::friendlyName()], 
			$model
		);
		$this->afterUpdateFormCreated($request, $form);
		$form->end();
		return ['form' => $form->renderAsString()];
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
	 * @param  Form    $form
	 */
	protected function afterUpdateFormCreated(Request $request, Form $form){}

	/**
	 * @inheritDoc
	 */
	public function update(Request $request, BaseModel $model): array
	{	
		$validated = $this->validateUpdateRequest($request, $model);

		try{
			$model->saveWithRelations($validated);
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
		return $model->validateForm($request->post(), $model->editFormFields());
	}

	/**
	 * Called before destroying a model
	 * @param  Request       $request
	 * @param  BaseModel $model
	 */
	protected function onDestroying(Request $request, BaseModel $model)
	{}

	/**
	 * @inheritDoc
	 */
	public function destroy(Request $request, BaseModel $model): array
	{
		$this->onDestroying($request, $model);
		$model = $model->delete();
		return $this->onSuccessfullDeletion($request);
	}

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
	 * @inheritDoc
	 */
	public function store(Request $request): array
	{
		$modelStr = $this->getModel();
		$model = new $modelStr;

		$validated = $this->validateStoreRequest($request, $model);

		try{
			$model->saveWithRelations($validated);
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
	 * Validates a request and return validated array
	 * @param  Request   $request
	 * @param  BaseModel $model 
	 * @return array
	 */
	protected function validateStoreRequest(Request $request, BaseModel $model)
	{
		return $model->validateForm($request->post(), $model->addFormFields());
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
	 * [afterSuccessfullStore description]
	 * @param  Request       $request [description]
	 * @param  BaseModel $model   [description]
	 * @return [type]                 [description]
	 */
	protected function onStoreSuccess(Request $request, BaseModel $model)
	{
		return ['model' => $model, 'message' => $model::friendlyName()." has been created"];
	}

	/**
	 * @inheritDoc
	 */
	public function get(Request $request, BaseModel $model): BaseModel
	{
		return $model;
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
	 * @inheritDoc
	 */
	public function create(Request $request): array
	{	
		$model = $this->getModel();
		$form = new FormModel(
			['url' => $this->getStoreUri($request), 'method' => 'POST'], 
			['submit' => ['Save'], 'view' => 'forms.modal', 'title' => 'Add a '.$model::friendlyName()], 
			$model
		);
		$this->afterStoreFormCreated($request, $form);
		$form->end();
		return ['form' => $form->renderAsString()];
	}

	protected function afterStoreFormCreated(Request $request, Form $form){}

	/**
	 * @inheritDoc
	 */
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
			$item->refresh();
			$models[] = $item;

		}
		return $this->onSuccessfullPatch($request, $models);
	}

	public function onSuccessfullPatch(Request $request, Collection $models): array
	{
		return ['message' => str_plural($this->getModel()::friendlyName())." have been updated", 'models' => $models];
	}
}
