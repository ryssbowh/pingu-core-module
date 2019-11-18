<?php

namespace Pingu\Core\Contracts;

use Pingu\Core\Support\Uris;

interface HasUrisContract
{
    public static function uris(): Uris;
}