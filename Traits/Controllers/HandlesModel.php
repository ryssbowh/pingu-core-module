<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Traits\Controllers\CreatesModel;
use Pingu\Core\Traits\Controllers\EditsModel;

trait HandlesModel
{
	use EditsModel, CreatesModel;
}