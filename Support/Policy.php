<?php 

namespace Pingu\Core\Support;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Traits\Macroable;
use Pingu\User\Entities\User;

class Policy
{
    use Macroable, HandlesAuthorization;

    protected function userOrGuest(?User $user)
    {
        return $user ? $user : \Permissions::guestRole();
    }
}