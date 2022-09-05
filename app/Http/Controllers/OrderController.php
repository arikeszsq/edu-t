<?php


namespace App\Http\Controllers;


use App\Http\Services\ActivityService;
use App\Http\Services\GroupService;
use App\Models\ActivityGroup;
use App\Models\ActivitySignUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function lists(Request $request)
    {
        $inputs = $request->all();
        $user_id = self::authUserId();
        $inputs['uid'] = $user_id;

        $validator = \Validator::make($inputs, [
            'activity_id' => 'required',
        ], [
            'activity_id.required' => '活动ID必填',
        ]);
        if ($validator->fails()) {
            return self::parametersIllegal($validator->messages()->first());
        }
        try {
            $query = DB::table('activity_sign_user')
                ->leftJoin('activity_group', function ($query) {
                    $query->on('activity_sign_user.group_id', 'activity_group.id');
                })
                ->leftJoin('users', 'activity_group.leader_id', 'users.id')
                ->where('activity_sign_user.activity_id', $inputs['activity_id'])
                ->where('activity_sign_user.user_id', $inputs['uid']);

            if (isset($inputs['finished']) && $inputs['finished']) {
                $query->where('activity_group.finished', $inputs['finished']);
            }

            if (isset($inputs['status']) && $inputs['status']) {
                $query->where('activity_sign_user.status', $inputs['status']);
            }

            $orders = $query->get();
            $data = [];
            foreach ($orders as $order) {
                $data[] = [
                    'avatar' => $order->avatar,
                    'name' => $order->sign_name,
                    'order_no' => $order->order_no,
                    'pay_time' => $order->pay_time,
                    'group_success_time' => $order->success_time,
                    'status'=>$order->status,
                    'finished'=>$order->finished,
                ];
            }
            $ret['num_unfinished'] = DB::table('activity_sign_user')
                ->leftJoin('activity_group', 'activity_sign_user.group_id', 'activity_group.id')
                ->where('activity_sign_user.activity_id', $inputs['activity_id'])
                ->where('activity_sign_user.user_id', $user_id)->where('activity_group.finished', ActivityGroup::Finished_成团)
                ->count();
            $ret['num_finished'] = DB::table('activity_sign_user')
                ->leftJoin('activity_group', 'activity_sign_user.group_id', 'activity_group.id')
                ->where('activity_sign_user.activity_id', $inputs['activity_id'])
                ->where('activity_sign_user.user_id', $user_id)->where('activity_group.finished', ActivityGroup::Finished_未成团)
                ->count();
            $ret['list'] = $data;
            return self::success($ret);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }
}
