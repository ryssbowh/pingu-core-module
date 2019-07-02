<?php

namespace Pingu\Core\Http\Controllers;

use Illuminate\Http\Request;
use Pingu\Core\Contracts\Models\HasAjaxRoutesContract;
use Pingu\Core\Exceptions\ControllerException;
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
		$model = $this->getModel();
		$model = new $model;
		if(!($model instanceof FormableContract)){
			throw ControllerException::modelMissingInterface($model, FormableContract::class);
		}
		if(!($model instanceof HasAjaxRoutesContract)){
			throw ControllerException::modelMissingInterface($model, HasAjaxRoutesContract::class);
		}
		parent::__construct($request);
	}
}

?>