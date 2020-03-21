<?php 

namespace Pingu\Core\Support;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Traits\Macroable;

class Policy
{
    use Macroable, HandlesAuthorization;
}