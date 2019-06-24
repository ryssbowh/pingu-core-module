<?php 
namespace Pingu\Core\Traits\Models;

use Pingu\Core\Exceptions\UriReplacementsSize;

trait HasAdminRoutes {

	/**
	 * @inheritDoc
	 */
	public static function adminIndexUri()
	{
		return self::routeSlugs();
	}

	/**
	 * @inheritDoc
	 */
	public static function adminPatchUri()
	{
		return self::routeSlugs();
	}

	/**
	 * @inheritDoc
	 */
	public static function adminCreateUri()
	{
		return self::routeSlugs().'/create';
	}

	/**
	 * @inheritDoc
	 */
	public static function adminStoreUri()
	{
		return self::routeSlugs();
	}

	/**
	 * @inheritDoc
	 */
	public static function adminEditUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}/edit';
	}

	/**
	 * @inheritDoc
	 */
	public static function adminUpdateUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}';
	}

	/**
	 * route for delete requests
	 * 
	 * @return string
	 */
	public static function adminDeleteUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}';
	}

	/**
	 * @inheritDoc
	 */
	public static function adminConfirmDeleteUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}/delete';
	}

	/**
	 * Gets an uri, prefixed with '/api/' or not
	 * @param  string $action
	 * @param  $prefixed bool
	 * @return ?string
	 */
	public static function getAdminUri(string $action, $prefixed = false)
	{	
		$method = 'admin'.ucfirst($action).'Uri';
		if(method_exists(__CLASS__, $method)){
			return ($prefixed ? config('core.adminPrefix') : '').static::$method();
		}
		return null;
	}

	/**
	 * Transform an uri, replacing all slugs by values in array
	 * @param  string  $action
	 * @param  boolean $prefixed
	 * @return ?string
	 */
	public static function transformAdminUri(string $action, $replacements, $prefixed = false)
	{
		$replacements = (is_array($replacements) ? $replacements : [$replacements]);
		$method = 'admin'.ucfirst($action).'Uri';
		if(method_exists(__CLASS__, $method)){
			$uri = ($prefixed ? config('core.adminPrefix') : '').static::$method();
			return replaceUriSlugs($uri, $replacements);
		}
		return null;
	}
}