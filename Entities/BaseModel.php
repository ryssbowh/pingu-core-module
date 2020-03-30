<?php

namespace Pingu\Core\Entities;

use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Pingu\Core\Exceptions\FieldNotFillable;
use Pingu\Core\Traits\Models\HasFieldsFriendlyNames;
use Pingu\Core\Traits\Models\HasFieldsFriendlyValues;
use Pingu\Core\Traits\Models\HasFriendlyName;
use Pingu\Core\Traits\Models\ThrowsEvents;
use Pingu\Field\Contracts\HasFieldsContract;
use Pingu\Field\Traits\HasBaseFields;
use Pingu\Forms\Contracts\FormRepositoryContract;
use Pingu\Forms\Contracts\HasFormsContract;
use Pingu\Forms\Support\BaseForms;

abstract class BaseModel extends Model implements HasFieldsContract, HasFormsContract
{
    use ThrowsEvents,
        HasBaseFields,
        HasFriendlyName,
        HasFieldsFriendlyValues,
        HasFieldsFriendlyNames,
        FormAccessible;

    protected $fillable = [];

    public static $friendlyName;

    protected static $recordEvents = ['created','updated','deleted'];

    public $descriptiveField = 'id';

    /**
     * Short description for this object
     * 
     * @return string
     */
    public function getDescription(): string
    {
        return $this->{$this->descriptiveField};
    }

    /**
     * @inheritDoc
     */
    public function identifier(): string
    {
        return 'model-'.class_machine_name($this);
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

    /**
     * @inheritDoc
     */
    public function forms(): FormRepositoryContract
    {
        return new BaseForms($this);
    }

    /**
     * Get all attributes (including bundle fields)
     * 
     * @return array
     */
    public function getAllAttributes()
    {
        return $this->getAttributes();
    }

    /**
     * Get all original (including bundle fields)
     * 
     * @return array
     */
    public function getAllOriginal()
    {
        return $this->getOriginal();
    }
}
