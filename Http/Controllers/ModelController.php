<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use JsGrid,ContextualLinks,Notify;
use Modules\Core\Entities\BaseModel;
use Modules\Forms\Components\FormModel;

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
		$model->formFill($validator->validated());
		$model->save();
		if($model->getChanges()){
			Notify::put('success', $model::friendlyName().' updated successfully');
		}
		else{
			Notify::put('info', 'No changes done to '.$model::friendlyName());
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
		$validator->validate();
		$model->formFill($validator->validated());
		if($model->save()){
			Notify::put('success', $model::friendlyName().' created successfully');
		}
		else{
			Notify::put('info', 'Error while saving '.$model::friendlyName());
		}
		return back();
	}
}
