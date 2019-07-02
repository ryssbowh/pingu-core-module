<?php

namespace Pingu\Core\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


interface HasChildrenContract
{
	public function parent(): BelongsTo;

	public function children(): HasMany;

	public function hasChildren(): bool;

	public function hasParent(): bool;

}