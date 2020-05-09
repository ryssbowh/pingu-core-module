<?php

namespace Pingu\Core\Components;

class Policies
{
   /**
    * Registered policies
    * 
    * @var array
    */
    protected $policies = [];

    /**
     * Registers a policy in the Gate system
     * 
     * @param string|object $class
     * @param string $policy
     */
    public function register($class, string $policy)
    {
        $class = object_to_class($class);
        $this->policies[$class] = $policy;
        \Gate::policy($class, $policy);
    }

    /**
     * Get the policy for an object
     * 
     * @param string|object $object
     * 
     * @return string
     */
    public function get($object): string
    {
        $class = object_to_class($object);
        if (isset($this->policies[$class])) {
            return $this->policies[$class];
        }
    }

    /**
     * Get all registered policies
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->policies;
    }
}