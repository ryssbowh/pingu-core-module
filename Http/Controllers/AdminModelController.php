<?php

namespace Pingu\Core\Http\Controllers;

use Illuminate\Http\Request;
use Pingu\Core\Contracts\Models\HasAdminRoutesContract;
use Pingu\Core\Exceptions\ControllerException;
use Pingu\Core\Traits\Controllers\CreatesAdminModel;
use Pingu\Core\Traits\Controllers\DeletesAdminModel;
use Pingu\Core\Traits\Controllers\EditsAdminModel;
use Pingu\Forms\Contracts\Models\FormableContract;

abstract class AdminModelController extends ModelController
{	
	use EditsAdminModel, CreatesAdminModel, DeletesAdminModel;

	public function __construct(Request $request)
	{
		$model = $this->getModel();
		$model = new $model;
		if(!($model instanceof FormableContract)){
			throw ControllerException::modelMissingInterface($model, FormableContract::class);
		}
		if(!($model instanceof HasAdminRoutesContract)){
			throw ControllerException::modelMissingInterface($model, HasAdminRoutesContract::class);
		}
		parent::__construct($request);
	}
}

?>