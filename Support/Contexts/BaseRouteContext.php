<?php

namespace Pingu\Core\Support\Contexts;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Pingu\Core\Contracts\RouteContexts\RouteContextContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Exceptions\ContextException;
use Pingu\Core\Exceptions\ParameterMissing;
use Pingu\Core\Traits\Controllers\InteractsWithRoute;

abstract class BaseRouteContext implements RouteContextContract
{
    use InteractsWithRoute;

    /**
     * @var object
     */
    protected $object;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(object $object)
    {
        $this->object = $object;
    }

    /**
     * Set the request for the context
     * 
     * @param Request $request
     */
    public function setRequest(Request $request): RouteContextContract
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Check that the request has been set
     * 
     * @return 
     */
    protected function checkRequest()
    {
        if (is_null($this->request)) {
            throw ContextException::noRequest($this);
        }
    }

    /**
     * Does the request want Json
     * 
     * @return bool
     */
    protected function wantsJson(): bool
    {
        return $this->request->wantsJson();
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