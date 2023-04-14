<?php

namespace App\Models;


use App\User;
use Illuminate\Database\Eloquent\Model;

class UserAward extends Model
{

    protected $table = 'user_award';

    public function activity()
    {
        return $this->hasOne(Activity::class, 'id', 'activity_id');
    }

    public function award()
    {
        return $this->hasOne(Award::class, 'id', 'award_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public static function hasGetAlready($activity_id, $user_id, $award_id)
    {
        $has = UserAward::query()
            ->where('activity_id', $activity_id)
            ->where('user_id', $user_id)
            ->where('award_id', $award_id)
            ->first();
        return $has ? 1 : 0;
    }

}
