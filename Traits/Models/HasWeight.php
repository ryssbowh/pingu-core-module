<?php 
namespace Pingu\Core\Traits\Models;

use Pingu\Core\Traits\HasCrudUris;

trait HasWeight {

	/**
	 * Field in db that holds the weight
	 * @var string
	 */
	protected static $weightField = 'weight';

	/**
	 * get next weight in db
	 *
	 * @param array $parameters
	 * @return int
	 */
	public static function getNextWeight(array $parameters = [])
	{
		if(is_null($weight = static::getLastWeight($parameters))){
			return 0;
		}
		return $weight + 1;
	}

	/**
	 * get last weight in db
	 *
	 * @param array $parameters
	 * @return ?int
	 */
	public static function getLastWeight(array $parameters = [])
	{
		$field = static::$weightField;
		$model = static::where($parameters)->orderBy($field, 'DESC')->first();
		if(!$model){
			return null;
		}
		return $model->$field;
	}

}