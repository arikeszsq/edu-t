<?php

namespace App\Models;


use App\User;
use Illuminate\Database\Eloquent\Model;

class ActivitySignUser extends Model
{

    protected $table = 'activity_sign_user';


    public function activity()
    {
        return $this->hasOne(Activity::class,'id','activity_id');
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
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

    Const Status_已支付=3;

    Const Role_团长=1;
    Const Role_团员=2;


    public static function getHasPayList($activity_id)
    {
        return ActivitySignUser::query()
            ->with('user')
            ->where('activity_id',$activity_id)
            ->where('status',ActivitySignUser::Status_已支付)
            ->orderBy('pay_time','desc')
            ->get();
    }

}
