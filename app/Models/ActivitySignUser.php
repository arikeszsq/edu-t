<?php

namespace App\Models;


use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ActivitySignUser extends Model
{

    protected $table = 'activity_sign_user';

    public static function getActivitySignUserById($id)
    {
        return ActivitySignUser::query()->find($id);
    }

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


    public function courses()
    {
        return $this->hasMany(ActivitySignUserCourse::class, 'order_num', 'order_no');
    }

    const Sex_List = [
        1 => '男',
        2 => '女'
    ];

    const Status_支付 = [
        1 => '待支付',
        2 => '支付取消',
        3 => '支付成功',
        4 => '已退款'
    ];

    const type_支付 = [
        1 => '开团支付',
        2 => '单独购买',
    ];

    const Status_已支付 = 3;

    const Role_团长 = 1;
    const Role_团员 = 2;


    const Type_团 = 1;
    const Type_直接买 = 2;

    public static function getHasPayList($activity_id)
    {
        return ActivitySignUser::query()
            ->with('user')
            ->where('activity_id', $activity_id)
            ->where('status', ActivitySignUser::Status_已支付)
            ->orderBy('pay_time', 'desc')
            ->limit(3)
            ->get();
    }

    /**
     * 最后一步支付前，完善用户信息
     * @param $inputs
     * @return int
     */
    public static function createOrder($inputs)
    {
        $activity_id = $inputs['activity_id'];
        $activity = Activity::getActivityById($activity_id);
        $is_many = $activity->is_many;
        $data = [
            'activity_id' => $activity_id,
            'group_id' => isset($inputs['group_id']) && $inputs['group_id'] ? $inputs['group_id'] : 0,
            'role' => isset($inputs['group_id']) && $inputs['group_id'] ? 2 : 1,
            'type' => $inputs['type'],//1开团 2单独购买
            'creater_id' => $inputs['uid'],
            'order_no' => $inputs['order_num'],
            'user_id' => $inputs['uid'],
            'has_pay' => 2,//1是 2 否
            'status' => 1,//待支付
            'money' => $inputs['money'],
            'created_at' => date('Y-m-d H:i:s', time()),
        ];
        if ($is_many == Activity::is_many_多商家) {
            $data_many = [
                'info' => json_encode($inputs['info']),
                'course_ids' => $inputs['course_ids'],
                'school_ids' => $inputs['school_ids'],
                'award_ids' => $inputs['award_ids'],
            ];
            self::createUserCourse($inputs);
            $order = ActivitySignUser::query()->insertGetId(array_merge($data, $data_many));
        } else {
            $data_one = [
                'info' => $inputs['info'],
            ];
            $order = ActivitySignUser::query()->insertGetId(array_merge($data, $data_one));
        }
        return $order;
    }

    /**
     * 新建订单时，添加用户选择的课程
     * @param $inputs
     * @return bool
     */
    public static function createUserCourse($inputs)
    {
        $course_ids = $inputs['course_ids'];
        $school_child_ids = $inputs['school_ids'];
        $course_ids_array = explode(',', $course_ids);
        $school_child_ids_array = explode(',', $school_child_ids);
        $activity_sign_user_course = [];
        foreach ($course_ids_array as $key => $value) {
            $activity_sign_user_course[] = [
                'activity_id' => $inputs['activity_id'],
                'school_id' => $school_child_ids_array[$key],
                'course_id' => $value,
                'user_id' => $inputs['uid'],
                'order_num' => $inputs['order_num']
            ];
        }
        return DB::table('activity_sign_user_course')->insert($activity_sign_user_course);
    }


    /**
     * 支付成功后回调
     * @param $order_no
     */
    public static function updatePayInfo($order_no)
    {
        ActivitySignUser::query()->where('order_no', $order_no)
            ->update([
                'has_pay' => 1,
                'status' => 3,
                'pay_time' => Carbon::now(),
            ]);
    }

    /**
     * 支付成功后回调
     * @param $order_no
     */
    public static function getHasPayNum($activity_id)
    {
        return ActivitySignUser::query()
            ->where('activity_id', $activity_id)
            ->where('has_pay', 1)
            ->count();;
    }

}
