<?php
namespace Pingu\Core\Exceptions;

class RouteParameterException extends \Exception{

	public function __construct($key){
		parent::__construct("parameter $key doesn't exist in route");
	}
}