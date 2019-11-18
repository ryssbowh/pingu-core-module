<?php

namespace Pingu\Core\Components;

use Pingu\Core\Support\Actions as ActionsSupport;

class Actions
{   
    protected $actionsInstances = [];

    public function register($class, ActionsSupport $actions)
    {
        $class = object_to_class($class);
        $this->actionsInstances[$class] = $actions;
    }

    public function get($class)
    {
        $class = object_to_class($class);
        if (isset($this->actionsInstances[$class])) {
            return $this->actionsInstances[$class];
        }
    }

    public function all()
    {
        return $this->actionsInstances;
    }
}