<?php

namespace Pingu\Core\Traits\Controllers;

use Illuminate\Http\Request;
use Pingu\Core\Contracts\RouteContexts\HasRouteContextContract;
use Pingu\Core\Contracts\RouteContexts\RouteContextContract;
use Pingu\Core\Contracts\RouteContexts\ValidatableContextContract;

trait HasRouteContexts
{
    /**
     * Resolve the request context from an object
     * 
     * @param HasRouteContextContract $object
     * @param string             $fallback
     * 
     * @return FieldContextContract
     */
    protected function getRouteContext(HasRouteContextContract $object, Request $request): RouteContextContract
    {
        $contexts = request()->route()->getAction('context');
        $context = $object->getRouteContext($contexts);
        return $context->setRequest($request);
    }

    /**
     * Resolve the validatable request context from an object
     * 
     * @param HasRouteContextContract $object
     * 
     * @return FieldContextContract
     */
    protected function getValidatableContext(HasRouteContextContract $object, Request $request): ValidatableContextContract
    {
        return $this->getRouteContext($object, $request);
    }
}