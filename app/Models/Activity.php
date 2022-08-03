<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{

    const Status_已上架 = 1;
    const Status_已下架 = 2;

    public static  $status_list = [
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
        return $this->hasMany(ActivitySignCom::class,'activity_id','id');
    }

}
