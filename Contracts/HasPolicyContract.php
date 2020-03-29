<?php

namespace Pingu\Core\Contracts;

interface HasPolicyContract
{
    /**
     * Policy class for this entity
     * 
     * @return string
     */
    public function getPolicy(): string;
}