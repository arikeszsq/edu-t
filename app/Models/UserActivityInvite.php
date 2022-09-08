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

    public static function updatePayInfo($order)
    {
        $user_id = $order->user_id;
        $activity_id = $order->activity_id;
        UserActivityInvite::query()->where('activity_id', $activity_id)->where('invited_user_id', $user_id)
            ->orderBy('id', 'desc')->update([
                'has_pay' => 1
            ]);
    }
}
