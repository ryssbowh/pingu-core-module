<?php

namespace Pingu\Core\Contracts\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Pingu\Core\Entities\BaseModel;

interface HandlesAjaxModelContract extends UsesModelContract
{
	/**
	 * api entry point for getting models.
	 * @param  Request $request
	 * @return array
	 */
	public function index(Request $request): array;

	/**
	 * Get an edit form
	 * @param  Request $request
	 * @param  BaseModel $model
	 * @return BaseModel
	 */
	public function edit(Request $request, BaseModel $model): array;

	/**
	 * Updates a model
	 * @param  Request $request
	 * @param  BaseModel $model
	 * @return BaseModel
	 */
	public function update(Request $request, BaseModel $model): array;

	/**
	 * Deletes a model
	 * @param  Request $request
	 */
	public function destroy(Request $request, BaseModel $model);

	/**
	 * Gets a model
	 * @param  Request       $request
	 * @param  BaseModel $model
	 * @return BaseModel $model         
	 */
	public function get(Request $request, BaseModel $model): BaseModel;

	/**
	 * Stores a model
	 * @param  Request $request
	 * @return BaseModel $model
	 */
	public function store(Request $request): array;

	/**
	 * create request
	 * @param  Request $request
	 * @return array
	 */
	public function create(Request $request): array;

	/**
	 * Patch requests, post must be an array of array, each specifying the primary key of the object being patched
	 * @param  Request $request
	 * @return array
	 */
	public function patch(Request $request): array;
	
}
