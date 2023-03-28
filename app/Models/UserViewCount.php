<?php

namespace App\Models;


use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserViewCount extends Model
{

    protected $table = 'user_view_count';

    public function activity()
    {
        return $this->hasOne(Activity::class, 'id', 'activity_id');
    }

    public function shareUser()
    {
        return $this->hasOne(User::class, 'id', 'share_user_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public static function addViewLog($activity_id, $user_id, $share_user_id)
    {
        $user_view = UserViewCount::query()
            ->where('activity_id', $activity_id)
            ->where('user_id', $user_id)
            ->first();

        if ($user_view) {
            UserViewCount::query()->where('id', $user_view->id)->update([
                'view_num' => $user_view->view_num + 1,
                'view_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            UserViewCount::query()->insert([
                'activity_id' => $activity_id,
                'user_id' => $user_id,
                'has_info' => 0,
                'has_sign' => 0,
                'view_num' => 1,
                'share_user_id' => $share_user_id,
                'view_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }


    /**
     * 获取活动浏览数
     * @param $id
     * @return int|mixed
     */
    public static function getActivityViewNum($id)
    {
        return UserViewCount::query()->where('activity_id', $id)->sum('view_num');
    }
}
