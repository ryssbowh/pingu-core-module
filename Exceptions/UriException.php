<?php
namespace Pingu\Core\Exceptions;

use Pingu\Entity\Contracts\Uris;

class UriException extends \Exception{

	public static function undefined(string $uri, Uris $class)
	{
		return new static("Uri '$uri' isn't defined in ".get_class($class));
	}
}