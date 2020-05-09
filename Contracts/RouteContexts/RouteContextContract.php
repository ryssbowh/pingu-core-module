<?php

namespace Pingu\Core\Contracts\RouteContexts;

use Illuminate\Http\Request;
use Pingu\Core\Entities\BaseModel;

interface RouteContextContract
{
    /**
     * Name for this context
     * 
     * @return string
     */
    public static function scope(): string;

    /**
     * Set the request to handle by this context
     * 
     * @param Request $request
     */
    public function setRequest(Request $request);

    /**
     * Get response for a request
     * 
     * @return mixed
     */
    public function getResponse();
}