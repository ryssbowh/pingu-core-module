<?php

namespace Pingu\Core\Traits\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Pingu\User\Entities\User;

trait DeletedBy
{
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public static function bootDeletedBy()
    {
        static::deleting(
            function ($model) {
                $model->deletedBy()->associate(\Auth::user());
            }
        );

        static::restoring(
            function ($model) {
                $model->deletedBy()->dissociate();
            }
        );
    }
}