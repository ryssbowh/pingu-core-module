<?php

namespace Pingu\Core\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Pingu\Forms\Contracts\FormableModel;

interface ApiModelController extends UsesModel
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
	 * @param  FormableModel $model
	 * @return FormableModel
	 */
	public function edit(Request $request, FormableModel $model): array;

	/**
	 * Updates a model
	 * @param  Request $request
	 * @param  FormableModel $model
	 * @return FormableModel
	 */
	public function update(Request $request, FormableModel $model): array;

	/**
	 * Deletes a model
	 * @param  Request $request
	 */
	public function destroy(Request $request, FormableModel $model);

	/**
	 * Gets a model
	 * @param  Request       $request
	 * @param  FormableModel $model
	 * @return FormableModel $model         
	 */
	public function get(Request $request, FormableModel $model): FormableModel;

	/**
	 * Stores a model
	 * @param  Request $request
	 * @return FormableModel $model
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
