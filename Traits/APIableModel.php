<?php 
namespace Pingu\Core\Traits;

trait APIableModel {

	/**
	 * @inheritDoc
	 */
	public static function apiIndexUri()
	{
		return self::routeSlugs();
	}

	/**
	 * @inheritDoc
	 */
	public static function apiPatchUri()
	{
		return self::routeSlugs();
	}

	/**
	 * @inheritDoc
	 */
	public static function apiCreateUri()
	{
		return self::routeSlugs().'/create';
	}

	/**
	 * @inheritDoc
	 */
	public static function apiStoreUri()
	{
		return self::routeSlugs();
	}

	/**
	 * @inheritDoc
	 */
	public static function apiEditUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}/edit';
	}

	/**
	 * @inheritDoc
	 */
	public static function apiUpdateUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}';
	}

	/**
	 * @inheritDoc
	 */
	public static function apiDeleteUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}';
	}

	/**
	 * Gets an uri, prefixed with '/api/' or not
	 * @param  string $action
	 * @param  $prefixed bool
	 * @return ?string
	 */
	public static function getApiUri(string $action, $prefixed = false)
	{	
		$method = 'api'.ucfirst($action).'Uri';
		if(method_exists(__CLASS__, $method)){
			$uri = ($prefixed ? config('core.apiPrefix') : '').static::$method();
			return '/'.ltrim($uri, '/');
		}
		return null;
	}

	/**
	 * Transform an uri, replaceing all slugs by values in array
	 * @param  string  $action
	 * @param  boolean $prefixed
	 * @return ?string
	 */
	public static function transformApiUri(string $action, array $replacements, $prefixed = false)
	{
		$method = 'api'.ucfirst($action).'Uri';
		if(method_exists(__CLASS__, $method)){
			$uri = ($prefixed ? config('core.apiPrefix') : '').static::$method();
			return replaceUriSlugs($uri, $replacements);
		}
		return null;
	}
}