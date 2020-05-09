<?php

namespace Pingu\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Route;
use Pingu\Core\Exceptions\ParameterMissing;
use Pingu\Core\Exceptions\RouteActionNotSet;
use Pingu\Core\Exceptions\RouteParameterException;
use Pingu\Core\Traits\Controllers\InteractsWithRoute;

class BaseController extends Controller
{
    use InteractsWithRoute;

    /**
     * Global variable to access Request
     *
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request  = $request;
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
}

?>