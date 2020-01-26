<?php

namespace Pingu\Core\Traits\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Pingu\User\Entities\User;

trait CreatedBy
{
    /**
     * Created by relationship
     * 
     * @return BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Boots trait. Insert current user in created by field
     */
    public static function bootCreatedBy()
    {
        static::saving(
            function ($model) {
                $model->createdBy()->associate(\Auth::user());
            }
        );
    }
}