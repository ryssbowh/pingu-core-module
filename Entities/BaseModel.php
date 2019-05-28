<?php

namespace Pingu\Core\Entities;

use Greabock\Tentacles\EloquentTentacle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Pingu\Core\Exceptions\FieldNotFillable;
use Pingu\Core\Traits\ModelEventThrower;

class BaseModel extends Model
{
	use EloquentTentacle, ModelEventThrower;

    protected $fillable = [];

    public static $friendlyName;
    protected static $recordEvents = ['created','updated','deleted'];

    /**
     * Routes slugs (plural)
     * @return string
     */
    public static function routeSlugs()
    {
        return str_plural(Str::snake(class_basename(static::class)));
    }

    /**
     * Route slug (singular)
     * @return string
     */
    public static function routeSlug()
    {
        return Str::snake(class_basename(static::class));
    }

    /**
     * Model's friendly name
     * @return [type] [description]
     */
    public static function friendlyName()
    {
    	return static::$friendlyName ?? friendlyClassname(static::class);
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
     * @return  string
     */
    protected static function keyName() {
        return (new static)->getKeyName();
    }

    /**
     * Static acessible table name
     * @return string
     */
    protected static function tableName() {
        return (new static)->getTable();
    }
}
