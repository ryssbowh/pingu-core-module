<?php

namespace Pingu\Core\Entities;

use Pingu\Core\Settings\SettingsRepository;
use Pingu\Core\Traits\Models\HasWeight;

class Settings extends BaseModel
{
    use HasWeight;

    protected $guarded = ['name'];

    protected $primaryKey = 'name';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'encrypted' => 'bool'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($setting) {
            $setting->weight = $setting::getNextWeight(['repository' => $setting->repository]);
        });
        static::saved(function () {
            \Settings::forgetCache();
        });
    }

    public function repository(): SettingsRepository
    {
        return \Settings::repository($this->repository);
    }

    public function getValueAttribute($value)
    {
        if ($this->encrypted and $value) {
            $value = decrypt($value);
        }
        return $this->repository()->cast($this->name, $value);
    }

    public function setValueAttribute($value)
    {
        if ($this->encrypted and $value) {
            $value = encrypt($value);
        }
        $this->attributes['value'] = $value;
    }
}
