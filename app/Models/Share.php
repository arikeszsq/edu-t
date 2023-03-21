<?php

namespace App\Models;


use App\User;
use Illuminate\Database\Eloquent\Model;

class Share extends Model
{

    protected $table = 'share';

    public function activity()
    {
        return $this->hasOne(Activity::class, 'id', 'activity_id');
    }

    public function shareUser()
    {
        return $this->hasOne(User::class, 'id', 'share_user_id');
    }

    public static function shareNum($user_id, $activity_id)
    {
        return Share::query()->where('share_user_id', $user_id)
            ->where('activity_id', $activity_id)->value('share_num');
    }
}
