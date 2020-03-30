<?php

namespace Pingu\Core\Contracts;

use Illuminate\Database\Eloquent\Relations\Relation;


interface HasItemsContract
{
    /**
     * Item relationship
     * 
     * @return Relation
     */
    public function items(): Relation;
}