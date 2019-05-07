<?php

namespace Pingu\Core\Http\Controllers;

use Illuminate\Http\Request;
use ContextualLinks,Notify;
use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Form;

class ApiModelController extends Controller
{
	/**
	 * api entry point for getting models.
	 * if looking at a relatedModel, the query can be modified in the method relatedJsGrid{Model} on your related model.
	 * Example : if looking at a user's roles, the query can be modified in Role::relatedJsGridUser(Request, $user)
	 * @param  Request $request
	 * @return array
	 */
	public function index(Request $request){
		$model = $this->checkIfRouteHasModel($request);
		$model = new $model;

		$filters = $request->post()['filters'];
		$options = $request->post()['options'] ?? [];

		$fieldsDef = (new $model)->fieldDefinitions();
		$query = $model->newQuery();
		foreach($filters['fields'] as $field => $value){
			$fieldDef = $fieldsDef[$field];
			if(!is_null($value)){
				$fieldDef['type']::fieldQueryModifier($query, $field, $value);
			}
		}

		if(isset($options['relatedModel'])){
			$relatedModel = $options['relatedModel']::find($options['relatedId']);
			$method = 'relatedJsGrid'.classname($options['relatedModel']);
			$model::$method($query, $relatedModel);
		}

		$count = $query->count();

		if(isset($filters['sortField'])){
			$query->orderBy($filters['sortField'], $filters['sortOrder']);
		}

		if(isset($filters['pageIndex'])){
			$query->offset(($filters['pageIndex']-1) * $filters['pageSize'])->take($filters['pageSize']);
		}

		$models = $query->get();

		return ['data' => $models->toArray(), 'total' => $count];
	}

	/**
	 * Updates a model
	 * @param  Request $request
	 * @return BaseModel
	 */
	public function update(Request $request)
	{
		$model = $this->checkIfRouteHasModel($request);
		$post = $request->post();
		$model = $model::findOrFail($post['id']);
		$validator = $model->makeValidator($request, $model->editFormFields());
		$validator->validate();
		$model->formFill($validator->validated());
		$model->save();
		$model->saveRelationships($validator->validated());
		$model->refresh();
		return $model;
	}

	/**
	 * Deletes a model
	 * @param  Request $request
	 */
	public function destroy(Request $request)
	{
		$model = $this->checkIfRouteHasModel($request);
		$id = $request->post()['id'];
		$model = $model::findOrFail($id);
		$model->delete();
	}
}
