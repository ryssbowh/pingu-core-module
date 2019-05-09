<?php

namespace Pingu\Core\Contracts;

use Illuminate\Http\Request;
use Pingu\Forms\Contracts\FormableModel;

interface ModelController extends UsesModel
{
	/**
	 * Edit a model
	 * @param  Request $request
	 * @param  FormableModel $model
	 * @return view
	 */
	public function edit(Request $request, FormableModel $model);

	/**
	 * Updates a model
	 * @param  Request   $request
	 * @param  FormableModel $model
	 * @return redirect
	 */
	public function update(Request $request, FormableModel $model);

	/**
	 * Create form for a model. Model must be set within the route
	 * @param  Request $request
	 * @return view
	 */
	public function create(Request $request);

	/**
	 * Stores a new model, model must be set within the route
	 * @param  Request $request
	 * @return redirect
	 */
	public function store(Request $request);
}
