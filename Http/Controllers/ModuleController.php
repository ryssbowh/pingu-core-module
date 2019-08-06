<?php

namespace Pingu\Core\Http\Controllers;

class ModuleController extends BaseController
{
	public function index()
	{
		$modules = \Module::all();
		return view('core::modules')->with([
			'modules' => $modules
		]);
	}
}
