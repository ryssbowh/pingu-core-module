<?php

namespace Pingu\Core\Traits;

use ContextualLinks,Notify;
use Illuminate\Http\Request;
use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Contracts\FormableModel;
use Pingu\Forms\Form;

trait ApiModelController 
{
	/**
	 * api entry point for getting models.
	 * @param  Request $request
	 * @return array
	 */
	public function index(Request $request): array
	{
		$modelStr = $this->getModel();
		$model = new $modelStr;

		$filters = $request->post()['filters'];
		$options = $request->post()['options'] ?? [];

		$fieldsDef = $model->fieldDefinitions();
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
	 * @return FormableModel
	 */
	public function update(Request $request): FormableModel
	{
		$model = $this->getModel();
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
		$model = $this->getModel();
		$id = $request->post()['id'];
		$model = $model::findOrFail($id);
		$model->delete();
	}
}
