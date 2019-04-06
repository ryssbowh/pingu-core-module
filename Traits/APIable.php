<?php 
namespace Modules\Core\Traits;

trait APIable {

	public function apiableFields()
	{
		return array_merge(['id'], $this->fillable);
	}

	/**
	 * api route for API queries
	 * @return [type] [description]
	 */
	public static function apiUrl()
	{
		return '/api/'.self::urlSegments();
	}
}