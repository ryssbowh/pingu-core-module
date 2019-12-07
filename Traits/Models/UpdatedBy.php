<?php

namespace Pingu\Core\Traits\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Pingu\User\Entities\User;

trait UpdatedBy
{
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function bootUpdatedBy()
    {
        static::saving(
            function ($model) {
                $model->updatedBy()->associate(\Auth::user());
            }
        );
    }
}