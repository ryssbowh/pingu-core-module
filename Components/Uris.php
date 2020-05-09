<?php

namespace Pingu\Core\Components;

use Pingu\Core\Contracts\HasUrisContract;
use Pingu\Core\Support\Uris\Uris as UrisSupport;

class Uris
{
    /**
     * @var array
     */
    protected $urisInstances = [];

    /**
     * Get an instance of Uris for an object.
     * Will be created on the spot if not registered
     * 
     * @param HasUrisContract $object
     * 
     * @return UrisSupport
     */
    public function get(string $class): UrisSupport
    {
        if (!isset($this->urisInstances[$class])) {
            $this->register($class, $class::makeUrisInstance());
        }
        return $this->urisInstances[$class];
    }

    /**
     * Registers an Uris instance
     * 
     * @param string      $identifier
     * @param UrisSupport $uris
     */
    public function register(string $identifier, UrisSupport $uris)
    {
        $this->urisInstances[$identifier] = $uris;
    }
}