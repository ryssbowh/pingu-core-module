<?php

namespace Pingu\Core\Http\Controllers;

class CoreController extends BaseController
{
	public function home()
	{
		return view('core::home');
	}

	public function adminHome()
	{
		return view('core::adminHome');
	}
}
