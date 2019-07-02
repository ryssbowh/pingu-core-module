<?php

namespace Pingu\Content\Exceptions;

class ParameterMissing extends \Exception{

	public function __construct($field, $method){
		$message = $field." is missing in $method parameters";
		parent::__construct($message);
	}

}