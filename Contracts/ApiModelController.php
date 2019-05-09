<?php

namespace Pingu\Core\Contracts;

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
	 * Updates a model
	 * @param  Request $request
	 * @return BaseModel
	 */
	public function update(Request $request): FormableModel;

	/**
	 * Deletes a model
	 * @param  Request $request
	 */
	public function destroy(Request $request);
}
