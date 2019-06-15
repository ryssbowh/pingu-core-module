<?php
namespace Pingu\Core\Contracts\Controllers;

use Illuminate\Http\Request;
use Pingu\Core\Entities\BaseModel;

interface EditsModelContract extends UsesModelContract
{
	/**
	 * Edit a model
	 * @param  BaseModel $model
	 * @return view
	 */
	public function edit(BaseModel $model);

	/**
	 * Updates a model
	 * @param  BaseModel $model
	 * @return redirect
	 */
	public function update(BaseModel $model);
}