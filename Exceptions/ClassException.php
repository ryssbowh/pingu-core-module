<?php
namespace Pingu\Core\Exceptions;

class ClassException extends \Exception{

	public static function missingInterface(string $class, string $interface)
	{
		return new static($class." must implement ".$interface);
	}

}