<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Award extends Model
{
    const Status_有效 = 1;
    const Status_无效 = 2;
    const Status_list = [
        self::Status_有效 => '有效',
        self::Status_无效 => '无效'
    ];

    const Yes_是 = 1;
    const No_否 = 2;
    const Yes_1_No_2_list = [
        self::Yes_是 => '是',
        self::No_否 => '否'
    ];

    public function activity()
    {
        return $this->hasOne(Activity::class, 'id', 'activity_id');
    }


    /**
     * @param $award_id
     * @param $user_id
     * @return void
     * 用户是否可以领取奖励
     */
    public static function isUserCanGet($award_id, $user_id)
    {
        $award = Award::query()->find($award_id);
        $activity_id = $award->activity_id;
        if ($award->is_free != 1) {
            if ($award->is_commander == 1) {
                $is_commander = ActivityGroup::query()->where('leader_id', $user_id)
                    ->where('status', ActivityGroup::Status_有效_已支付)
                    ->first();
                if (!$is_commander) {
                    return '只有团长才可以领取';
                }
            }
            if ($award->group_ok == 1) {
                $is_group_ok = ActivitySignUser::query()->where('activity_id', $activity_id)
                    ->with('group')
                    ->where('user_id', $user_id)
                    ->where('has_pay', ActivitySignUser::Status_已支付)
                    ->first();
                if (!$is_group_ok || $is_group_ok->group->finished == 2) {
                    return '成团后才可以领取';
                }
            }
            if ($award->invite_num > 0) {
                $user_invite_num = UserActivityInvite::getUserInviteSuccessNum($activity_id, $user_id);
                $num = $user_invite_num;
                if ($num < $award->invite_num) {
                    return '邀请人数不足，无法领取';
                }
            }
        }
        return 200;
    }
}
