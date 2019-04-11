<?php

namespace Modules\Core\Entities;

use Greabock\Tentacles\EloquentTentacle;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ModelEventThrower;

class BaseModel extends Model
{
	use EloquentTentacle, ModelEventThrower;

    protected $fillable = [];

    public static $friendlyName;
    protected static $recordEvents = ['created','updated','deleted'];

    public static function urlSegment()
    {
        return kebab_case(self::friendlyName());
    }

    public static function urlSegments()
    {
        return str_plural(self::urlSegment());
    }

    public static function routeSlug()
    {
        return classname(static::class);
    }

    public static function friendlyName()
    {
    	return static::$friendlyName ?? friendlyClassname(static::class);
    }

    public function getContextualLinks()
    {
        return [];
    }

    public static function adminAddUrl()
    {
        return '/admin/'.self::urlSegments().'/create';
    }

    public static function adminEditUrl()
    {
        return '/admin/'.self::urlSegment();
    }
}
