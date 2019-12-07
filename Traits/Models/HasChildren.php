<?php

namespace Pingu\Core\Traits\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasChildren
{
    public static function bootHasChildren()
    {
        static::deleting(
            function ($model) {
                if($model->hasChildren()) {
                    $model->children->each(
                        function ($item, $ind) {
                            $item->parent()->dissociate();
                            $item->save();
                        }
                    );
                }
            }
        );
    }
    /**
     * A model can have children
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id')->orderBy('weight');
    }

    /**
     * A model can have a parent
     *
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
}