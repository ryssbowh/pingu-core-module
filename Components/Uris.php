<?php

namespace Pingu\Core\Components;

use Pingu\Core\Contracts\HasUrisContract;
use Pingu\Core\Contracts\RegistersUrisThroughFacade;
use Pingu\Core\Support\Uris as UrisSupport;

class Uris
{
   
    protected $urisInstances = [];

    public function register(string $class, UrisSupport $uris)
    {
        $this->urisInstances[$class] = $uris;
    }

    public function get($object)
    {
        $class = object_to_class($object);
        if (isset($this->urisInstances[$class])) {
            return $this->urisInstances[$class];
        }
    }

    public function all()
    {
        return $this->urisInstances;
    }
}