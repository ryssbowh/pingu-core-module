<?php

namespace Pingu\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Pingu\Core\Contracts\HasIdentifierContract;
use Pingu\Core\Contracts\HasUrisContract;
use Pingu\Core\Contracts\RouteContexts\HasRouteContextContract;
use Pingu\Core\Exceptions\FieldNotFillable;
use Pingu\Core\Support\Uris\BaseModelUris;
use Pingu\Core\Support\Uris\Uris;
use Pingu\Core\Traits\Models\HasFieldsFriendlyNames;
use Pingu\Core\Traits\Models\HasFieldsFriendlyValues;
use Pingu\Core\Traits\Models\HasFriendlyName;
use Pingu\Core\Traits\Models\HasRouteContexts;
use Pingu\Core\Traits\Models\HasUrisThroughFacade;
use Pingu\Core\Traits\Models\ThrowsEvents;
use Pingu\Field\Contracts\HasFieldsContract;
use Pingu\Field\Traits\HasBaseFields;
use Pingu\Forms\Contracts\FormRepositoryContract;
use Pingu\Forms\Contracts\HasFormsContract;
use Pingu\Forms\Support\BaseForms;
use Pingu\Forms\Traits\FormAccessible;

abstract class BaseModel extends Model implements 
    HasFieldsContract, 
    HasFormsContract, 
    HasRouteContextContract,
    HasUrisContract
{
    use ThrowsEvents,
        HasBaseFields,
        HasRouteContexts,
        HasFriendlyName,
        HasFieldsFriendlyValues,
        HasFieldsFriendlyNames,
        FormAccessible,
        HasUrisThroughFacade;

    protected $fillable = [];

    public static $friendlyName;

    protected static $recordEvents = ['created','updated','deleted'];

    public $descriptiveField = 'id';

    protected $observables = ['registered'];

    /**
     * Register a registered model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function registered($callback)
    {
        static::registerModelEvent('registered', $callback);
    }

    /**
     * @inheritDoc
     */
    protected static function defaultUrisInstance(): Uris
    {
        return new BaseModelUris(static::class);
    }
    
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
    public static function forms(): FormRepositoryContract
    {
        return new BaseForms;
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

    /**
     * Registers this entity
     */
    public function register()
    {
        $this->fireModelEvent('registered');
    }
}
