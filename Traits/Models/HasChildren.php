<?php

namespace Pingu\Core\Traits\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasChildren
{
	/**
     * A model can have children
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
    	return $this->hasMany(static::class, 'parent_id')->orderBy('weight');
    }

    /**
     * A model can have a parent
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
    	return $this->belongsTo(static::class, 'parent_id');
    }

    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }

    public function hasParent(): bool
    {
        return !is_null($this->parent);
    }

    public function delete()
    {
        if($this->hasChildren()){
            $this->children->each(function($item, $ind){
                $item->parent()->dissociate();
                $item->save();
            });
        }
        return parent::delete();
    }
}