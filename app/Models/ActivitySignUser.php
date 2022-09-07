<?php

namespace App\Models;


use App\User;
use Illuminate\Database\Eloquent\Model;

class ActivitySignUser extends Model
{

    protected $table = 'activity_sign_user';


    public function activity()
    {
        return $this->hasOne(Activity::class, 'id', 'activity_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function group()
    {
        return $this->hasOne(ActivityGroup::class, 'id', 'group_id');
    }

    const Sex_List = [
        1 => '男',
        2 => '女'
    ];

    const Status_支付 = [
        1 => '待支付',
        2 => '支付取消',
        3 => '支付成功'
    ];

    const type_支付 = [
        1 => '开团支付',
        2 => '单独购买',
    ];

    Const Status_已支付 = 3;

    Const Role_团长 = 1;
    Const Role_团员 = 2;


    Const Type_团 = 1;
    Const Type_直接买 = 2;

    public static function getHasPayList($activity_id)
    {
        return ActivitySignUser::query()
            ->with('user')
            ->where('activity_id', $activity_id)
            ->where('status', ActivitySignUser::Status_已支付)
            ->orderBy('pay_time', 'desc')
            ->get();
    }

    public static function updateSignUserInfo($inputs)
    {
        $activity_id = $inputs['activity_id'];
        $is_many = Activity::isMany($activity_id);
        if ($is_many == Activity::is_many_多商家) {
            $data = [
                'type' => $inputs['type'],
                'sign_name' => $inputs['sign_name'],
                'sign_mobile' => $inputs['sign_mobile'],
                'sign_age' => $inputs['sign_age'],
                'sign_sex' => $inputs['sign_sex'],
                'creater_id' => $inputs['uid'],
            ];
        } else {
            $data = [
                'type' => $inputs['type'],
                'creater_id' => $inputs['uid'],
            ];
        }
        return ActivitySignUser::query()
            ->where('activity_id', $activity_id)
            ->where('user_id', $inputs['uid'])
            ->where('has_pay', 2)
            ->orderBy('id', 'desc')
            ->update($data);
    }
}
