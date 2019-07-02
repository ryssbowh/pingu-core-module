<?php

namespace Pingu\Core\Http\Controllers;

use Illuminate\Http\Request;
use Pingu\Core\Exceptions\ControllerException;

abstract class ModelController extends BaseController
{	
	protected $model;

	public function __construct(Request $request)
	{
		$model = $this->getModel();
		$this->model = new $model;
		parent::__construct($request);
	}
	/**
	 * Gets the model associated to this controller
	 * 
	 * @return string
	 */
	abstract protected function getModel();
}

?>