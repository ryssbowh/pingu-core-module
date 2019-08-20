<?php

namespace Pingu\Core\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * global variable to access Request
	 * @var Request
	 */
	protected $request;

	/**
	 * Route parameters for quick access
	 * @var array
	 */
	protected $routeParameters;

	public function __construct(Request $request)
	{
		$this->request  = $request;
		$this->routeParameters = $request->route()->parameters();
	}

}

?>