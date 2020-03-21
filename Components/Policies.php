<?php

namespace Pingu\Core\Components;

class Policies
{
   
    protected $policies = [];

    public function register($class, string $policy)
    {
        $class = object_to_class($class);
        $this->policies[$class] = $policy;
        \Gate::policy($class, $policy);
    }

    public function get($object)
    {
        $class = object_to_class($object);
        if (isset($this->policies[$class])) {
            return $this->policies[$class];
        }
    }

    public function all()
    {
        return $this->policies;
    }
}