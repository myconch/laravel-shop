<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\UserAddress;

class UserAddressPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function own(User $user,UserAddress $address){
        //若收货地址的user_id与当前用户的id一致时，返回true，否则返回false
        return $address->user_id == $user->id;
    }
}
