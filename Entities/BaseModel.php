<?php

namespace Pingu\Core\Entities;

use Greabock\Tentacles\EloquentTentacle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Pingu\Core\Exceptions\FieldNotFillable;
use Pingu\Core\Traits\Models\ThrowsEvents;

class BaseModel extends Model
{
	use EloquentTentacle, ThrowsEvents;

    protected $fillable = [];

    public static $friendlyName;

    protected static $recordEvents = ['created','updated','deleted'];

    /**
     * Model's machine name
     * @return string
     */
    public static function machineName():string
    {
        return Str::studly(class_basename(static::class));
    }

    /**
     * Model's machine name
     * @return string
     */
    public static function machineNames():string
    {
        return Str::plural(static::machineName());
    }

    /**
     * Model's friendly name
     * @return string
     */
    public static function friendlyName(): string
    {
    	return static::$friendlyName ?? friendlyClassname(static::class);
    }

    /**
     * Model's friendly names
     * @return string
     */
    public static function friendlyNames(): string
    {
        return str_plural(static::friendlyName());
    }

    /**
     * Determine if the given attribute may be mass assigned.
     *
     * @param  string  $key
     * @return bool
     */
    public function isFillable($key)
    {
        $isFillable = parent::isFillable($key);
        if(!$isFillable){
            throw new FieldNotFillable("Field $key of ".get_class($this)." is not fillable");
        }
        return $isFillable;
    }

    /**
     * Static accessible getKeyName
     * 
     * @return  string
     */
    protected static function keyName() {
        return (new static)->getKeyName();
    }

    /**
     * Static accessible getRouteKeyName
     * 
     * @return  string
     */
    protected static function routeKeyName() {
        return (new static)->getRouteKeyName();
    }

    /**
     * Static acessible table name
     * @return string
     */
    protected static function tableName() {
        return (new static)->getTable();
    }
}
