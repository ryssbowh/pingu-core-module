<?php

namespace Pingu\Core\Http\Controllers;

class CoreController extends BaseController
{
	public function home()
	{
		// $finder = app()->make('view.finder');
		// dump($finder);
		// exit();
		return view('pages.home');
	}
}
