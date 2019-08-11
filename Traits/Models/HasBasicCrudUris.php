<?php 
namespace Pingu\Core\Traits\Models;

use Pingu\Core\Traits\HasCrudUris;

trait HasBasicCrudUris {

	use HasRouteSlug, HasCrudUris;

	public static function indexUri()
	{
		return self::routeSlugs();
	}

	public static function patchUri()
	{
		return self::routeSlugs();
	}

	public static function createUri()
	{
		return self::routeSlugs().'/create';
	}

	public static function storeUri()
	{
		return self::routeSlugs();
	}

	public static function editUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}/edit';
	}

	public static function updateUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}';
	}

	public static function deleteUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}';
	}

	public static function confirmDeleteUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}/delete';
	}
}