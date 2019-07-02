<?php 
namespace Pingu\Core\Traits\Models;

trait HasAjaxRoutes {

	/**
	 * @inheritDoc
	 */
	public static function ajaxIndexUri()
	{
		return self::routeSlugs();
	}

	/**
	 * @inheritDoc
	 */
	public static function ajaxPatchUri()
	{
		return self::routeSlugs();
	}

	/**
	 * @inheritDoc
	 */
	public static function ajaxCreateUri()
	{
		return self::routeSlugs().'/create';
	}

	/**
	 * @inheritDoc
	 */
	public static function ajaxStoreUri()
	{
		return self::routeSlugs();
	}

	/**
	 * @inheritDoc
	 */
	public static function ajaxEditUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}/edit';
	}

	/**
	 * @inheritDoc
	 */
	public static function ajaxUpdateUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}';
	}

	/**
	 * @inheritDoc
	 */
	public static function ajaxDeleteUri()
	{
		return self::routeSlug().'/{'.self::routeSlug().'}';
	}

	/**
	 * Gets an uri, prefixed with '/api/' or not
	 * @param  string $action
	 * @param  $prefixed bool
	 * @return ?string
	 */
	public static function getAjaxUri(string $action, $prefixed = false)
	{	
		$method = 'ajax'.ucfirst($action).'Uri';
		if(method_exists(__CLASS__, $method)){
			$uri = ($prefixed ? config('core.ajaxPrefix') : '').static::$method();
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
	public static function transformAjaxUri(string $action, $replacements, $prefixed = false)
	{
		$replacements = is_array($replacements) ? $replacements : [$replacements];
		$method = 'ajax'.ucfirst($action).'Uri';
		if(method_exists(__CLASS__, $method)){
			$uri = ($prefixed ? config('core.ajaxPrefix') : '').static::$method();
			return replaceUriSlugs($uri, $replacements);
		}
		return null;
	}
}