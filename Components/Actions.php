<?php

namespace Pingu\Core\Components;

use Pingu\Core\Support\Actions as ActionsSupport;

class Actions
{
    /**
     * @var array
     */
    protected $actionsInstances = [];

    /**
     * Registers an action class
     * 
     * @param string         $class
     * @param ActionsSupport $actions
     */
    public function register($class, ActionsSupport $actions)
    {
        $class = object_to_class($class);
        $this->actionsInstances[$class] = $actions;
    }

    /**
     * Get an action class for a class
     * 
     * @param string|object $class
     * 
     * @return ?ActionSupport
     */
    public function get($class): ?ActionsSupport
    {
        $class = object_to_class($class);
        if (isset($this->actionsInstances[$class])) {
            return $this->actionsInstances[$class];
        }
    }

    /**
     * Get all action classes
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->actionsInstances;
    }
}