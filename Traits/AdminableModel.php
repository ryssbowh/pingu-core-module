<?php 
namespace Pingu\Core\Traits;

use Pingu\Core\Exceptions\UriReplacementsSize;

trait AdminableModel {

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
	 * @inheritDoc
	 */
	public static function adminDeleteUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}';
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
	 * Transform an uri, replaceing all slugs by values in array
	 * @param  string  $action
	 * @param  boolean $prefixed
	 * @return ?string
	 */
	public static function transformAdminUri(string $action, array $replacements, $prefixed = false)
	{
		$method = 'admin'.ucfirst($action).'Uri';
		if(method_exists(__CLASS__, $method)){
			$uri = ($prefixed ? config('core.adminPrefix') : '').static::$method();
			return replaceUriSlugs($uri, $replacements);
		}
		return null;
	}
}