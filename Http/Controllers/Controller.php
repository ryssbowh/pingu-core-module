<?php

namespace Pingu\Core\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	protected function checkIfRouteHasModel(Request $request)
	{
		if(!isset($request->route()->action['model'])) throw new \Exception('model is not set for that route');
		return $request->route()->action['model'];
	}

	public function home()
	{
		return view('core::home');
	}

	public function adminHome()
	{
		return view('core::adminHome');
	}
}

?>