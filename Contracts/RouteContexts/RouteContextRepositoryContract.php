<?php

namespace Pingu\Core\Contracts\RouteContexts;

use Pingu\Core\Contracts\RouteContexts\RouteContextContract;
use Pingu\Core\Contracts\RouteContexts\RouteContextRepositoryContract;

interface RouteContextRepositoryContract
{
    /**
     * Add a context
     * 
     * @param string $context
     */
    public function add(string $context): RouteContextRepositoryContract;

    /**
     * Add many context
     * 
     * @param array $contexts
     */
    public function addMany(array $contexts): RouteContextRepositoryContract;

    /**
     * Is a context defined for a scope
     * 
     * @param string  $scope
     * 
     * @return boolean
     */
    public function has(string $scope): bool;

    /**
     * Get the context for a object and a scope
     * 
     * @param object $object
     * @param string    $scope
     * 
     * @return FieldContextContract
     */
    public function get(object $object, string $scope): RouteContextContract;

    /**
     * Get the first defined route context
     * 
     * @param object $object
     * @param array     $scopes
     * 
     * @return RouteContextContract
     */
    public function getFirst(object $object, array $scopes): RouteContextContract;
}