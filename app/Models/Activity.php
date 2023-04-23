<?php

namespace App\Models;


use App\Http\Traits\MoneyCountTrait;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{

    const Status_已上架 = 1;
    const Status_已下架 = 2;

    const is_many_单商家 = 1;
    const is_many_多商家 = 2;

    public static $is_many_list = [
        Activity::is_many_单商家 => '单商家',
        Activity::is_many_多商家 => '多商家'
    ];


    public static $status_list = [
        Activity::Status_已上架 => '已上架',
        Activity::Status_已下架 => '已下架'
    ];

    protected $table = 'activity';

    public static function getActivityById($id)
    {
        return Activity::query()->find($id);
    }

    public function activityCompany()
    {
        return $this->hasMany(ActivitySignCom::class, 'activity_id', 'id');
    }

    public static function getActivityListOptions()
    {
        $activities = Activity::query()
//            ->where('start_time', '>', Carbon::now())
//            ->where('status', 1)//上架
            ->orderBy('id', 'desc')
            ->get();
        $options = [];
        foreach ($activities as $activity) {
            $options[$activity->id] = $activity->title;
        }
        return $options;
    }

    public static function isManyById($id)
    {
        $activity = self::getActivityById($id);
        return $activity->is_many;
    }

    public static function getViewNum($activity_id)
    {
        return UserViewCount::query()->where('activity_id', $activity_id)->sum('view_num');
    }

    public static function getSignSuccessNum($activity_id)
    {
        return ActivitySignUser::query()->where('activity_id', $activity_id)
            ->where('status', ActivitySignUser::Status_已支付)
            ->count();
    }

    public static function getSignSuccessMoney($activity_id)
    {
        return ActivitySignUser::query()->where('activity_id', $activity_id)
            ->where('status', ActivitySignUser::Status_已支付)
            ->sum('money');
    }

    public static function getRefundNum($activity_id)
    {
        return ActivitySignUser::query()->where('activity_id', $activity_id)
            ->where('status', ActivitySignUser::Status_已退款)
            ->count();
    }

    public static function getRefundTotalMoney($activity_id)
    {
        return ActivitySignUser::query()->where('activity_id', $activity_id)
            ->where('status', ActivitySignUser::Status_已退款)
            ->sum('money');
    }

    /**
     * @param $activity_id
     * @return int|mixed
     * 已返利总金额
     * 也就是别人已经提现的钱
     */
    public static function getTotalRBMoney($activity_id)
    {
        return UserApplyCashOut::query()
            ->where('status', '!=', UserApplyCashOut::Status_审核状态_拒绝)
            ->where('activity_id', $activity_id)
            ->sum('apply_money');
    }

}
