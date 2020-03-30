<?php

namespace Pingu\Core\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


interface HasChildrenContract
{
    /**
     * Parent relationship
     * 
     * @return BelongsTo
     */
    public function parent(): BelongsTo;

    /**
     * Children relation ship
     * 
     * @return HasMany
     */
    public function children(): HasMany;

    /**
     * Does this model have children
     * 
     * @return boolean
     */
    public function hasChildren(): bool;

    /**
     * Does this model have a parent
     * 
     * @return boolean
     */
    public function hasParent(): bool;

}