<?php

namespace Pingu\Core\Traits\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait IndexesAjaxModel 
{
	/**
	 * Indexes models
	 * 
	 * @param  Request $request [description]
	 * @return array
	 */
	public function index(Request $request): array
	{
		$this->beforeIndex();

		$filters = $request->input('filters', []);
		$options = $request->input('options', []);
		$pageIndex = $request->input('pageIndex', 1);
		$pageSize = $request->input('pageSize', $this->model->getPerPage());
		$sortField = $request->input('sortField', $this->model->getKeyName());
		$sortOrder = $request->input('sortOrder', 'asc');

		$fieldsDef = $this->model->getFieldDefinitions();
		$query = $this->model->newQuery();
		foreach($filters as $field => $value){
			if(!isset($fieldsDef[$field])){
				continue;
			}
			$fieldDef = $fieldsDef[$field];
			
			if(!is_null($value)){
				$fieldDef->option('type')->filterQueryModifier($query, $field, $value);
			}
		}

		$this->modifyIndexQuery($query);

		$count = $query->count();

		if($sortField){
			$query->orderBy($sortField, $sortOrder);
		}

		$query->offset(($pageIndex-1) * $pageSize)->take($pageSize);

		$models = $this->treatModelsForResponse($query->get());

		return ['models' => $models->toArray(), 'total' => $count];
	}

	/**
	 * Perform operations on models before they are returned to the client
	 * 
	 * @param  Collection $models
	 * @return Collection
	 */
	public function treatModelsForResponse(Collection $models)
	{
		return $models;
	}

	/**
	 * Modify the index query
	 * 
	 * @param Builder $query
	 */
	public function modifyIndexQuery(Builder $query){}

	/**
	 * Actions before indexing
	 */
	public function beforeIndex(){}
}
