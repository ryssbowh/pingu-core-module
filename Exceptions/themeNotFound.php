<?php 
namespace Pingu\Core\Exceptions;

class themeNotFound extends \Exception{

	public function __construct($message) {
		parent::__construct($message);
	}

}