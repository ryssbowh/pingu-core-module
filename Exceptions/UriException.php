<?php
namespace Pingu\Core\Exceptions;

class UriException extends \Exception{

	public static function undefined(string $uri, string $class)
	{
		return new static("Uri '$uri' isn't defined in ".$class);
	}

	public static function replacements(int $replacements, int $matches, string $uri)
	{
		return new static("Size of replacements (".$replacements.") doesn't match the size of replaceable entities (".$matches.") in $uri");
	}
}