<?php

namespace App\Http\Traits;

use App\Models\Activity;
use App\Models\UserActivityInvite;
use App\Models\UserApplyCashOut;
use Carbon\Carbon;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Http\StreamResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait MoneyCountTrait
{
    /***
     * 一场活动，用户所有的审核中和审核通过的提现金额
     * @param $user_id
     * @param $activity_id
     * @return int|mixed
     */
    public function historyCashOutTotalMoney($user_id, $activity_id)
    {
        return UserApplyCashOut::query()
            ->where('status', '!=', UserApplyCashOut::Status_审核状态_拒绝)
            ->where('activity_id', $activity_id)
            ->where('user_id', $user_id)
            ->sum('apply_money');
    }

    /***
     * 一场活动，一个用户的所有金额，一共三种奖励金额
     * @param $user_id
     * @param $activity_id
     * @return float|int
     */
    public function getAllMoney($user_id, $activity_id)
    {
        $activity = Activity::query()->find($activity_id);
        $a_invite_money = $activity->a_invite_money;
        //自己是老师，并且直接邀请的奖励
        $A_money_lists_num = UserActivityInvite::query()
            ->where('activity_id', $activity_id)
            ->where('A_user_id', $user_id)
            ->where('parent_user_id', $user_id)
            ->where('has_pay', 1)
            ->count();
        $money = 0;
        $money += $A_money_lists_num * $a_invite_money;

        //自己是老师，别人邀请，你邀请的奖励
        $A_other_money_lists_num = UserActivityInvite::query()
            ->where('activity_id', $activity_id)
            ->where('has_pay', 1)
            ->where('A_user_id', $user_id)
            ->where('parent_user_id', '!=', $user_id)
            ->count();
        $a_other_money = $activity->a_other_money;
        $money += $A_other_money_lists_num * $a_other_money;

        //自己不是老师，自己直接邀请的奖励
        $money_lists_num = UserActivityInvite::query()
            ->where('activity_id', $activity_id)
            ->where('has_pay', 1)
            ->where('A_user_id', '!=', $user_id)
            ->where('parent_user_id', $user_id)
            ->count();
        $second_invite_money = $activity->second_invite_money;
        $money += $money_lists_num * $second_invite_money;
        return $money;

    }
}
