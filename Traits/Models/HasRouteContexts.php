<?php

namespace Pingu\Core\Traits\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Pingu\Core\Contracts\RouteContexts\RouteContextContract;
use Pingu\Core\Contracts\RouteContexts\RouteContextRepositoryContract;
use Pingu\Core\Support\Contexts\ObjectContextRepository;

trait HasRouteContexts
{
    /**
     * Default field contexts
     * @var array
     */
    public static $routeContexts = [];

    /**
     * @inheritDoc
     */
    public static function contextRepositoryClass(): RouteContextRepositoryContract
    {
        return new ObjectContextRepository(static::$routeContexts);
    }

    /**
     * Resolve context repository instance
     * 
     * @return RouteContextRepositoryContract
     */
    protected function getContextRepository(): RouteContextRepositoryContract
    {
        return \RouteContext::getRepository($this);
    }

    /**
     * @inheritDoc
     */
    public static function addRouteContext($context)
    {
        \RouteContext::addContext(static::class, $context);
    }

    /**
     * @inheritDoc
     */
    public static function hasRouteContext(string $scope)
    {
        \RouteContext::getRepository(static::class)->has($scope);
    }

    /**
     * @inheritDoc
     */
    public function getRouteContext($scopes): RouteContextContract
    {
        if (is_array($scopes)) {
            return $this->getContextRepository()->getFirst($this, $scopes);
        }
        return $this->getContextRepository()->get($this, $scopes);
    }
}