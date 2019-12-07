<?php

namespace Pingu\Core\Components;

class Policies
{
   
    protected $policies = [];

    public function register(string $class, string $policy)
    {
        $this->policies[$class] = $policy;
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

    public function registerInGate()
    {
        foreach ($this->policies as $object => $policy) {
            \Gate::policy($object, $policy);
        }
    }
}