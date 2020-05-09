<?php

namespace Pingu\Core\Contracts\RouteContexts;

use Pingu\Core\Contracts\RouteContexts\RouteContextContract;
use Pingu\Core\Contracts\RouteContexts\RouteContextRepositoryContract;
use Pingu\Field\Contracts\FieldContextContract;

interface HasRouteContextContract
{
    /**
     * Add a field context to this class
     * 
     * @param string|array $context
     */
    public static function addRouteContext($context);

    /**
     * Does this class have a field context
     * 
     * @param string $name
     * 
     * @return boolean
     */
    public static function hasRouteContext(string $scope);

    /**
     * Get a field context for this class
     * 
     * @param string|array $name
     * 
     * @return RouteContextContract
     */
    public function getRouteContext($scope): RouteContextContract;

    /**
     * Context repository instance
     * 
     * @return RouteContextRepositoryContract
     */
    public static function contextRepositoryClass(): RouteContextRepositoryContract;
}