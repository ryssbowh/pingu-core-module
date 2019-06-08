<?php
namespace Pingu\Core\Contracts\Controllers;

use Illuminate\Http\Request;

interface CreatesModelContract extends UsesModelContract
{
	/**
	 * Create form for a model. Model must be set within the route
	 * @param  Request $request
	 * @return view
	 */
	public function create();

	/**
	 * Stores a new model, model must be set within the route
	 * @param  Request $request
	 * @return redirect
	 */
	public function store();
}