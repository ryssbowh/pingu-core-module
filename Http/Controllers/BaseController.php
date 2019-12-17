<?php

namespace Pingu\Core\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Route;
use Pingu\Core\Exceptions\ParameterMissing;
use Pingu\Core\Exceptions\RouteActionNotSet;
use Pingu\Core\Exceptions\RouteParameterException;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Global variable to access Request
     *
     * @var Request
     */
    protected $request;

    /**
     * Global variable to access route
     *
     * @var Route
     */
    protected $route;

    public function __construct(Request $request)
    {
        $this->request  = $request;
        $this->route = $request->route();
    }

    /**
     * Requires an input parameter
     * 
     * @param string $name
     * 
     * @return mixed
     * @throws ParameterMissing
     */
    protected function requireParameter(string $name)
    {
        $data = $this->request->input($name, null);
        if (is_null($data)) {
            throw new ParameterMissing($name);
        }
        return $data;
    }

    /**
     * Requires a route parameter
     * 
     * @param string $key
     * 
     * @return mixed
     */
    protected function routeParameter(string $key)
    {
        if(is_int($key)) {
            $parameters = array_keys($this->route->parameters);
            if(isset($parameters[$key])) {
                $key = $parameters[$key];
            }
        }
        if($key and isset($this->route->parameters[$key])) {
            return $this->route->parameters[$key];
        }
        throw new RouteParameterException($key);
    }

    /**
     * Requires a route action
     * 
     * @param string $name
     * 
     * @return mixed
     */
    protected function getRouteAction(string $name)
    {
        $actions = $this->route->action;
        if(!isset($actions[$name])) {
            throw new RouteActionNotSet($this, $name);
        }
        return $actions[$name];
    }

}

?>