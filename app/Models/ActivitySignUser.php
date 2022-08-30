<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ActivitySignUser extends Model
{

    protected $table = 'activity_sign_user';


    public function activity()
    {
        return $this->hasOne(Activity::class,'id','activity_id');
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

}
