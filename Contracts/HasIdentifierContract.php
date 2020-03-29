<?php

namespace Pingu\Core\Contracts;

interface HasIdentifierContract
{
    /**
     * Unique identifier for this object for internal use
     * 
     * @return string
     */
    public function identifier(): string;

}