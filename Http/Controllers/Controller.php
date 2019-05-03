<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
	protected function checkIfRouteHasModel(Request $request)
	{
		if(!isset($request->route()->action['model'])) throw new Exception('model is not set for that route');
		return $request->route()->action['model'];
	}
}

?>