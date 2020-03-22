<?php

namespace Pingu\Core\Entities;

use Greabock\Tentacles\EloquentTentacle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Pingu\Core\Exceptions\FieldNotFillable;
use Pingu\Core\Traits\Models\HasFieldsFriendlyNames;
use Pingu\Core\Traits\Models\HasFieldsFriendlyValues;
use Pingu\Core\Traits\Models\HasRouteSlug;
use Pingu\Core\Traits\Models\ThrowsEvents;
use Pingu\Field\Contracts\FieldRepository;
use Pingu\Field\Contracts\FieldsValidator;
use Pingu\Field\Contracts\HasFields;
use Pingu\Field\Traits\HasBaseFields;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Traits\Models\HasForms;

abstract class BaseModel extends Model implements HasFields
{
    use ThrowsEvents,
        HasBaseFields,
        HasForms,
        HasRouteSlug,
        HasFieldsFriendlyValues,
        HasFieldsFriendlyNames;

    protected $fillable = [];

    public static $friendlyName;

    protected static $recordEvents = ['created','updated','deleted'];

    protected $descriptiveField = 'id';

    public function getDescription()
    {
        return $this->{$this->descriptiveField};
    }

    /**
     * Model's friendly name
     * 
     * @return string
     */
    public static function friendlyName(): string
    {
        return static::$friendlyName ?? friendly_classname(static::class);
    }

    /**
     * Model's friendly names
     * 
     * @return string
     */
    public static function friendlyNames(): string
    {
        return str_plural(static::friendlyName());
    }

    /**
     * Determine if the given attribute may be mass assigned.
     *
     * @param string $key
     * 
     * @return bool
     */
    public function isFillable($key)
    {
        $isFillable = parent::isFillable($key);
        if (!$isFillable) {
            throw new FieldNotFillable("Field $key of ".get_class($this)." is not fillable");
        }
        return $isFillable;
    }

    /**
     * Static accessible getKeyName
     * 
     * @return string
     */
    protected static function keyName() 
    {
        return (new static)->getKeyName();
    }

    /**
     * Static accessible getRouteKeyName
     * 
     * @return string
     */
    protected static function routeKeyName() 
    {
        return (new static)->getRouteKeyName();
    }

    /**
     * Static acessible table name
     * 
     * @return string
     */
    protected static function tableName() 
    {
        return (new static)->getTable();
    }

    public function getAllAttributes()
    {
        return $this->getAttributes();
    }

    public function getAllOriginal()
    {
        return $this->getOriginal();
    }
}
