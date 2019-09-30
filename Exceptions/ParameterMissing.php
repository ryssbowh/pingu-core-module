<?php

namespace Pingu\Core\Exceptions;

class ParameterMissing extends \Exception{

	public function __construct($field, $method){
		$message = $field." is missing in $method parameters";
		parent::__construct($message);
	}

}