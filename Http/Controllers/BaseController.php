<?php

namespace Pingu\Core\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Route;
use Pingu\Core\Exceptions\RouteParameterException;
use Pingu\Entity\Exceptions\RouteActionNotSet;

class BaseController extends Controller
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * global variable to access Request
	 * @var Request
	 */
	protected $request;

	/**
	 * global variable to access Request
	 * @var Route
	 */
	protected $route;

	/**
	 * Route parameters for quick access
	 * @var array
	 */
	protected $routeParameters;

	public function __construct(Request $request)
	{
		$this->request  = $request;
		$this->route = $request->route();
	}

	protected function routeParameter($key)
	{
		if(is_int($key)){
			$parameters = array_keys($this->route->parameters);
			if(isset($parameters[$key])){
				$key = $parameters[$key];
			}
		}
		if($key and isset($this->route->parameters[$key])){
			return $this->route->parameters[$key];
		}
		throw new RouteParameterException($key);
	}

	protected function getRouteAction(string $name)
	{
		$actions = $this->route->action;
		if(!isset($actions[$name])){
			throw new RouteActionNotSet($this, $name);
		}
		return $actions[$name];
	}

}

?>