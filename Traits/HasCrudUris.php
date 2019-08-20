<?php 
namespace Pingu\Core\Traits;

use Pingu\Core\Exceptions\UriException;
use Pingu\Core\Exceptions\UriReplacementsSize;

trait HasCrudUris {

	/**
	 * Gets an uri by calling the associate static method on this object.
	 * The method name must start with the action wanted, followed by 'Uri'
	 * example 'deleteUri'
	 * 
	 * @param  string $action
	 * @param  ?string $prefix
	 * @return string
	 * @throws UriException
	 */
	public static function getUri(string $action, ?string $prefix = null)
	{	
		$method = $action.'Uri';
		$prefix = $prefix ? trim($prefix, '/').'/' : '';
		if(method_exists(__CLASS__, $method)){
			return '/'.$prefix.trim(static::$method(), '/');
		}
		throw UriException::undefined($action, get_called_class());
	}

	/**
	 * Transform an uri, replacing all slugs by values in replacements array
	 * 
	 * @param  string  $action
	 * @param  mixed $replacements
	 * @param  ?string $prefix
	 * @return string
	 */
	public static function makeUri(string $action, $replacements = [], ?string $prefix = null)
	{
		$replacements = (is_array($replacements) ? $replacements : [$replacements]);
		$uri = static::getUri($action, $prefix);
		return replaceUriSlugs($uri, $replacements);
	}
}