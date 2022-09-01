<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;

trait UserTrait
{
    /**
     * get auth user
     *
     * @return mixed
     */
    public static function authUser()
    {
        return Auth::guard('user')->user();
    }

    /**
     * get auth user id
     *
     * @return int|null
     */
    public static function authUserId()
    {
        return self::authUser() ? self::authUser()->id : null;
    }

    public static function authUserEmail()
    {
        return self::authUser() ? self::authUser()->email : null;
    }
}
