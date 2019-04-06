<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use JsGrid,ContextualLinks,Notify;
use Modules\Core\Entities\BaseModel;
use Modules\Forms\Components\Form;

class Controller extends BaseController
{
	protected function checkIfRouteHasModel(Request $request)
	{
		if(!isset($request->route()->action['model'])) throw new Exception('model is not set for that route');
		return $request->route()->action['model'];
	}
}

?>