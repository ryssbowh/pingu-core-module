<?php

namespace Pingu\Core\Traits;

use ContextualLinks,Notify;
use Illuminate\Http\Request;
use Pingu\Core\Contracts\HasContextualLinks;
use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Contracts\FormableModel;
use Pingu\Forms\Form;
use Pingu\Forms\FormModel;

trait ModelController
{

	/**
	 * Edit a model
	 * @param  Request $request
	 * @param  FormableModel $model
	 * @return view
	 */
	public function edit(Request $request, FormableModel $model)
	{
		$this->addContextualLinks($model);
		$form = $this->getEditForm($request, $model);
		return $this->getEditView($form, $model, $request);
	}

	/**
	 * Return the view for an edit request
	 * @param  FormModel      $form
	 * @param  BaseModel $model 
	 * @param  Request   $request
	 * @return view
	 */
	protected function getEditView(FormModel $form, BaseModel $model, Request $request)
	{
		return view('pages.editModel')->with([
			'form' => $form,
			'model' => $model,
		]);
	}

	/**
	 * Builds the form for an edit request
	 * @param  Request   $request
	 * @param  FormableModel $model 
	 * @return FormModel
	 */
	protected function getEditForm(Request $request, FormableModel $model)
	{
		$url = $this->getUpdateUrl($request, $model);
		$attrs = ['method' => 'PUT', 'url' => $url];
		$form = new FormModel($attrs, [],  $model);
		$form->end();
		return $form;
	}

	/**
	 * Get the url for an update request
	 * @param  Request   $request
	 * @param  FormableModel $model
	 * @return string
	 */
	public function getUpdateUrl(Request $request, FormableModel $model)
	{
		return $request->requestUri;
	}

	/**
	 * Add contextual links for edit requests
	 * @param BaseModel $model
	 */
	protected function addContextualLinks(BaseModel $model)
	{
		if($model instanceof HasContextualLinks) ContextualLinks::addModelLinks($model);
	}

	/**
	 * Updates a model
	 * @param  Request   $request
	 * @param  FormableModel $model
	 * @return mixed
	 */
	public function update(Request $request, FormableModel $model)
	{
		$validated = $this->validateUpdateRequest($request, $model);

		try{
			$changes = $model->saveWithRelations($validated);
			if($changes){
				$this->onModelSavedChanges($request, $model);
			}
			else{
				$this->onModelSavedNoChanges($request, $model);
			}
		}
		catch(ModelNotSaved $e){
			$this->onUpdateFailure($request, $model, $e);
		}
		catch(ModelRelationsNotSaved $e){
			$this->onUpdateRelationshipsFailure($request, $model, $e);
		}

		return $this->afterUpdate($request, $model);
	}

	/**
	 * Callback after store action, this is where you redirect users.
	 * @return mixed
	 */
	protected function afterUpdate(Request $request, BaseModel $model)
	{
		return back();
	}

	/**
	 * Callback after model is sucessfully added
	 * @param  Request $request
	 * @param  BaseModel $model
	 */
	protected function onUpdateSuccess(Request $request, BaseModel $model)
	{
		Notify::success($model::friendlyName().' has been saved');
	}

	/**
	 * Callback when model can't be saved
	 * @param  Request   $request
	 * @param  BaseModel $model
	 * @param  ModelNotSaved $exception 
	 */
	protected function onUpdateFailure(Request $request, BaseModel $model, ModelNotSaved $exception)
	{
		Notify::error('Error while saving '.$model::friendlyName());
	}

	/**
	 * Callback when model's relationships can't be saved
	 * @param  Request   $request
	 * @param  BaseModel $model
	 * @param  ModelRelationsNotSaved $exception 
	 */
	protected function onUpdateRelationshipsFailure(Request $request, BaseModel $model, ModelRelationsNotSaved $exception)
	{
		Notify::error($model::friendlyName().' was partially saved, check manually');
	}

	/**
	 * Validates a request and return validated array
	 * @param  Request   $request
	 * @param  FormableModel $model 
	 * @return array
	 */
	protected function validateUpdateModelRequest(Request $request, FormableModel $model)
	{
		return $model->validateForm($request->post(), $model->editFormFields());
	}

	/**
	 * Callback when a model is saved without changes
	 * @param  Request   $request
	 * @param  BaseModel $model
	 */
	public function onModelSavedNoChanges(Request $request, BaseModel $model)
	{
		Notify::info('No changes made to '.$model::friendlyName());
	}

	/**
	 * Callback when a model is saved with changes
	 * @param  Request   $request
	 * @param  BaseModel $model
	 */
	public function onModelSavedChanges(Request $request, BaseModel $model)
	{
		Notify::success($model::friendlyName().' has been saved');
	}

	/**
	 * Create form for a model. Model must be set within the route
	 * @param  Request $request
	 * @return view
	 */
	public function create(Request $request)
	{
		$form = $this->getCreateForm($request);

		return $this->getCreateView($form, $request);
	}

	/**
	 * Get the view for a create request
	 * @param  FormModel $form
	 * @param  string $model
	 * @param  Request   $request
	 * @return view
	 */
	protected function getCreateView(FormModel $form, Request $request)
	{
		return view('pages.addModel')->with([
			'form' => $form,
			'model' => $this->getModel(),
		]);
	}

	/**
	 * Builds the form for a create request
	 * @param  Request $request
	 * @param  string  $model
	 * @return FormModel
	 */
	protected function getCreateForm(Request $request)
	{
		
		$url = $this->getStoreUrl($request);

		$attrs = ['method' => 'POST', 'url' => $url];
		$form = new FormModel($attrs, [], $this->getModel());
		$form->end();

		return $form;
	}

	/**
	 * Get the url for a store request
	 * @param  Request $request
	 * @return string
	 */
	protected function getStoreUrl(Request $request)
	{
		$segments = request()->segments();
		array_pop($segments);
		return '/'.implode($segments,'/');
	}

	/**
	 * Stores a new model, model must be set within the route
	 * @param  Request $request
	 * @return redirect
	 */
	public function store(Request $request)
	{
		$modelStr = $this->getModel();
		$model = new $modelStr;

		$validated = $this->validateStoreRequest($request, $model);

		try{
			$model->saveWithRelations($validated);
			$this->onStoreSuccess($request, $model);
		}
		catch(ModelNotSaved $e){
			$this->onStoreFailure($request, $model, $e);
		}
		catch(ModelRelationsNotSaved $e){
			$this->onStoreRelationshipsFailure($request, $model, $e);
		}

		return $this->afterStore($request, $model);
	}

	/**
	 * Callback after store action, this is where you redirect users.
	 * @return mixed
	 */
	protected function afterStore(Request $request, BaseModel $model)
	{
		return back();
	}

	/**
	 * Validates a request and return validated array
	 * @param  Request   $request
	 * @param  FormableModel $model 
	 * @return array
	 */
	protected function validateStoreRequest(Request $request, FormableModel $model)
	{
		return $model->validateForm($request->post(), $model->addFormFields());
	}

	/**
	 * Callback after model is sucessfully added
	 * @param  Request $request
	 * @param  BaseModel $model
	 */
	protected function onStoreSuccess(Request $request, BaseModel $model)
	{
		Notify::success($model::friendlyName().' has been saved');
	}

	/**
	 * Callback when model can't be saved
	 * @param  Request   $request
	 * @param  BaseModel $model
	 * @param  ModelNotSaved $exception 
	 */
	protected function onStoreFailure(Request $request, BaseModel $model, ModelNotSaved $exception)
	{
		Notify::info('Error while saving '.$model::friendlyName());
	}

	/**
	 * Callback when model's relationships can't be saved
	 * @param  Request   $request
	 * @param  BaseModel $model
	 * @param  ModelRelationsNotSaved $exception 
	 */
	protected function onStoreRelationshipsFailure(Request $request, BaseModel $model, ModelRelationsNotSaved $exception)
	{
		Notify::info($model::friendlyName().' was partially saved, check manually');
	}

}
