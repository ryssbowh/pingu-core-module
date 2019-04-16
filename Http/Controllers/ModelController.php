<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use JsGrid,ContextualLinks,Notify;
use Modules\Core\Entities\BaseModel;
use Modules\Forms\FormModel;

class ModelController extends Controller
{
	/**
	 * Edit a model
	 * @param  Request $request
	 * @param  BaseModel $model
	 * @return view
	 */
	public function edit(Request $request, BaseModel $model){
		$attrs = ['method' => 'PUT', 'url' => $request->requestUri];
		ContextualLinks::addLinks($model->getContextualLinks());
		$form = new FormModel($attrs, [],  $model);
		$form->end();
		return view('pages.editObject')->with([
			'form' => $form,
			'object' => $model::friendlyName(),
		]);
	}

	/**
	 * Updates a model
	 * @param  Request   $request
	 * @param  BaseModel $model
	 * @return redirect
	 */
	public function update(Request $request, BaseModel $model)
	{
		$validator = $model->makeValidator($request);
		$validator->validate();
		$validated = $validator->validated();

		try{
			$changes = $model->saveWithRelations($validated);
			if($changes){
				Notify::put('success', $model::friendlyName().' has been saved');
			}
			else{
				Notify::put('info', 'No changes made to '.$model::friendlyName());
			}
		}
		catch(ModelNotSaved $e){
			Notify::put('error', 'Error while saving '.$model::friendlyName());
		}
		catch(ModelRelationsNotSaved $e){
			Notify::put('error', $model::friendlyName().' was partially saved, check manually');
		}

		return back();
	}

	/**
	 * Create form for a model. Model must be set within the route
	 * @param  Request $request
	 * @return view
	 */
	public function create(Request $request)
	{
		$modelStr = $this->checkIfRouteHasModel($request);

		$segments = request()->segments();
		array_pop($segments);
		$url = '/'.implode($segments,'/');

		$attrs = ['method' => 'POST', 'url' => $url];
		$form = new FormModel($attrs, [], $modelStr);
		$form->end();
		return view('pages.addObject')->with([
			'form' => $form,
			'object' => $modelStr::friendlyName(),
		]);
	}

	/**
	 * Stores a new model, model must be set within the route
	 * @param  Request $request
	 * @return redirect
	 */
	public function store(Request $request)
	{
		$modelStr = $this->checkIfRouteHasModel($request);
		$model = new $modelStr;
		$validator = $model->makeValidator($request);
		$validated = $validator->validate();

		try{
			$model->saveWithRelations($validated);
			Notify::put('success', $model::friendlyName().' has been saved');
		}
		catch(ModelNotSaved $e){
			Notify::put('info', 'Error while saving '.$model::friendlyName());
		}
		catch(ModelRelationsNotSaved $e){
			Notify::put('info', $model::friendlyName().' was partially saved, check manually');
		}
	}
}
