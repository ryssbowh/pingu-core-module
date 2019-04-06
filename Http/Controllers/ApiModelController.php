<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use JsGrid,ContextualLinks,Notify,DB;
use Modules\Core\Entities\BaseModel;
use Modules\Forms\Components\Form;

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

		$where = [];
		$fieldsDef = (new $model)->fieldDefinitions();
		$query = $model->newQuery();
		foreach($filters['fields'] as $field => $value){
			$fieldDef = $fieldsDef[$field];
			if(!is_null($value)){
				$fieldDef['type']::queryFilterApi($query, $field, $value);
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

		$fields = array_keys($filters['fields']);
		if(!isset($fields['id'])) $fields[] = 'id';

		$data = [];
		foreach($query->get() as $model){
			$line = [];
			foreach($model->apiableFields() as $field){
				$line[$field] = $model->$field;
			}
			$data[] = $line;
		}

		return ['data' => $data, 'total' => $count];
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
		$return = array_keys($post);
		$model = $model::findOrFail($post['id']);
		unset($post['id']);
		$model->formFill($post);
		$model->save();
		return $model->only($return);
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
