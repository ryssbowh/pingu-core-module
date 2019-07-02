<?php

namespace Pingu\Core\Exceptions;

use Pingu\Core\Entities\BaseModel;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProtectedModel extends HttpException{

	public static function forDeletion(BaseModel $model)
	{
		return new static(403, "This ".$model::friendlyName()." can't be deleted");
	}

	public static function forEdition(BaseModel $model)
	{
		return new static(403, "This ".$model::friendlyName()." can't be edited");
	}

}