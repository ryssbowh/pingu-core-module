<?php

namespace Pingu\Core\Contracts;

use Illuminate\Database\Eloquent\Relations\Relation;


interface HasItems
{
	public function items(): Relation;
}