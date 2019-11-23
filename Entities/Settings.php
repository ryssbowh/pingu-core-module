<?php

namespace Pingu\Core\Entities;

use Pingu\Core\Traits\Models\HasWeight;

class Settings extends BaseModel
{
    use HasWeight;

    protected $guarded = ['name'];

    protected $primaryKey = 'name';

    public $incrementing = false;

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($setting) {
            $setting->weight = $setting::getNextWeight(['repository' => $setting->repository]);
        });
    }
}
