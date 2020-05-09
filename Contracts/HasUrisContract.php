<?php

namespace Pingu\Core\Contracts;

use Pingu\Core\Support\Uris\Uris;

interface HasUrisContract
{
    /**
     * Uris accessor
     * 
     * @return Uris
     */
    public static function uris(): Uris;
}