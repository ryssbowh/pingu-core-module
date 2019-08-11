<?php

namespace Pingu\Core\Http\Controllers;

use Illuminate\Http\Request;
use Pingu\Core\Contracts\Models\HasCrudUrisContract;
use Pingu\Core\Exceptions\ClassException;
use Pingu\Core\Traits\Controllers\CreatesAdminModel;
use Pingu\Core\Traits\Controllers\DeletesAdminModel;
use Pingu\Core\Traits\Controllers\EditsAdminModel;
use Pingu\Forms\Contracts\Models\FormableContract;

abstract class AdminModelController extends ModelController
{	
	use EditsAdminModel, CreatesAdminModel, DeletesAdminModel;

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