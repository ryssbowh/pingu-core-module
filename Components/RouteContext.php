<?php

namespace Pingu\Core\Components;

use Illuminate\Support\Arr;
use Pingu\Core\Contracts\RouteContexts\RouteContextRepositoryContract;
use Pingu\Core\Entities\BaseModel;

class RouteContext
{   
    /**
     * Context repositories
     * @var array
     */
    protected $repositories = [];

    /**
     * Get context repository for an object
     * 
     * @param string|object $object
     * 
     * @return FieldContextRepositoryContract
     */
    public function getRepository($object): RouteContextRepositoryContract
    {
        $class = object_to_class($object);
        if (!isset($this->repositories[$class])) {
            $this->repositories[$class] = $class::contextRepositoryClass();
        }
        return $this->repositories[$class];
    }

    /**
     * Add a context to an object
     * 
     * @param object|string $object
     * @param string|array $contexts
     */
    public function addContext($object, $contexts)
    {
        $this->getRepository($object)->addMany(Arr::wrap($contexts));
        return $this;
    }
}