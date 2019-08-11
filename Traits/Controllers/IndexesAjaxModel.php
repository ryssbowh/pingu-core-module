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
	public function index(): array
	{
		$this->beforeIndex();

		$filters = $this->request->input('filters', []);
		$options = $this->request->input('options', []);
		$pageIndex = $this->request->input('pageIndex', 1);
		$pageSize = $this->request->input('pageSize', $this->model->getPerPage());
		$sortField = $this->request->input('sortField', $this->model->getKeyName());
		$sortOrder = $this->request->input('sortOrder', 'asc');

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
