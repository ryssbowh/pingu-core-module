<?php
namespace Pingu\Core\Exceptions;

class UriException extends \Exception{

	public static function undefined(string $uri, string $class)
	{
		return new static("Uri '$uri' isn't defined in ".$class);
	}
}