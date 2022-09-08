<?php

namespace App\Models;


use App\Http\Traits\UserTrait;
use App\User;
use Illuminate\Database\Eloquent\Model;

class ActivityGroup extends Model
{
    use UserTrait;

    protected $table = 'activity_group';

    const Status_有效_已支付 = 1;
    const Status_无效_未支付 = 2;

    const Finished_成团 = 1;
    const Finished_未成团 = 2;

    public function user()
    {
        return $this->hasOne(User::class,'id','leader_id');
    }

    public static function getGroupById($id)
    {
        return ActivityGroup::query()->find($id);
    }

    public static function NewGroup($activity_id)
    {
        $user_id = self::authUserId();
        $activity = Activity::getActivityById($activity_id);
        $group = new ActivityGroup();
        $group->activity_id = $activity_id;
        $group->name = static::getOnlyCode($activity_id);
        $group->num = $activity->deal_group_num;
        $group->current_num = 1;
        $group->leader_id = $user_id;
        $group->creater_id = $user_id;
        $group->status = 2;
        $group->save();
    }

    public static function getOnlyCode($activity_id)
    {
        $code = $activity_id . '团-' . rand(11111111, 99999999);
        $first = ActivityGroup::query()->where('name', $code)->first();
        if ($first) {
            static::getOnlyCode($activity_id);
        }
        return $code;
    }

}
