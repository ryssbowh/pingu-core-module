<?php

namespace Pingu\Core\Http\Controllers;

use Illuminate\Http\Request;
use Pingu\Core\Contracts\Models\HasCrudUrisContract;
use Pingu\Core\Exceptions\ClassException;
use Pingu\Core\Traits\Controllers\CreatesAjaxModel;
use Pingu\Core\Traits\Controllers\DeletesAjaxModel;
use Pingu\Core\Traits\Controllers\EditsAjaxModel;
use Pingu\Core\Traits\Controllers\IndexesAjaxModel;
use Pingu\Core\Traits\Controllers\PatchesAjaxModel;
use Pingu\Forms\Contracts\Models\FormableContract;

abstract class AjaxModelController extends ModelController
{	
	use CreatesAjaxModel, EditsAjaxModel, DeletesAjaxModel, PatchesAjaxModel, IndexesAjaxModel;

	public function __construct(Request $request)
	{
		$modelStr = $this->getModel();
		$model = new $modelStr;
		if(!($model instanceof FormableContract)){
			throw ClassException::missingInterface($modelStr, FormableContract::class);
		}
		if(!($model instanceof HasCrudUrisContract)){
			throw ClassException::missingInterface($modelStr, HasCrudUrisContract::class);
		}
		parent::__construct($request);
	}
}

?>