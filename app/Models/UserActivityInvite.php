<?php

namespace App\Models;


use App\User;
use Illuminate\Database\Eloquent\Model;

class UserActivityInvite extends Model
{

    protected $table = 'user_activity_invite';


    public function activity()
    {
        return $this->hasOne(Activity::class, 'id', 'activity_id');
    }

    public function inviteUser()
    {
        return $this->hasOne(User::class, 'id', 'A_user_id');
    }

    public function parentUser()
    {
        return $this->hasOne(User::class, 'id', 'parent_user_id');
    }

    public function aUser()
    {
        return $this->hasOne(User::class, 'id', 'invited_user_id');
    }

    /***
     * 用户邀请成功的用户数:包括直接邀请，和间接邀请的总数
     * @param $activity_id
     * @param $user_id
     * @return int
     */
    public static function getUserInviteSuccessNum($activity_id, $user_id)
    {
        return UserActivityInvite::query()
            ->where('activity_id', $activity_id)
            ->where('has_pay', 1)
            ->where(function ($query) use ($user_id) {
                $query->where('A_user_id', $user_id)
                    ->orWhere('parent_user_id', $user_id);
            })->count();
    }
}
