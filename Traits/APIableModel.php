<?php 
namespace Modules\Core\Traits;

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