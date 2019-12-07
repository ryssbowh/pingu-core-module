<?php
namespace Pingu\Core\Exceptions;

use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Http\Controllers\BaseController;

class ControllerException extends \Exception
{

    public static function getModelUndefined(BaseController $controller)
    {
        return new static(get_class($controller)." must implement getModel():string method");
    }

}