<?php 
namespace Pingu\Core\Traits;

trait APIableModel {

	/**
	 * api route for API queries
	 * @return string
	 */
	public static function apiUrl()
	{
		return '/api/'.self::urlSegments();
	}
}