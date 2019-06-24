<?php

namespace Pingu\Core\Exceptions;

class AssetException extends \Exception{

	protected $output;

	public function __construct($message, $output)
	{
		$this->output = $output;
		$message = $message.". output : \n".implode("\n", $output);
		parent::__construct($message);
	}

	public function getOutput()
	{
		return $this->output;
	}

	public static function merging(array $output)
	{
		return new static("Package.json files could not be merged", $output);
	}

	public static function installing(array $output)
	{
		return new static("Could not install npm packages", $output);
	}

	public static function compiling(array $output)
	{
		return new static("Assets could not be compiled", $output);
	}

}