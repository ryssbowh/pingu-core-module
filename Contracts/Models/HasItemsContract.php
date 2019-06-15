<?php

namespace Pingu\Core\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\Relation;


interface HasItemsContract
{
	public function items(): Relation;
}