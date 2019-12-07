<?php

namespace Pingu\Core\Traits\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Pingu\User\Entities\User;

trait CreatedBy
{
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function bootCreatedBy()
    {
        static::saving(
            function ($model) {
                $model->createdBy()->associate(\Auth::user());
            }
        );
    }
}