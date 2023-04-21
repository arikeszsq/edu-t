<?php


namespace App\Http\Controllers;


use App\Models\ActivityGroup;
use App\Models\ActivitySignUser;
use App\Models\User;
use App\Models\UserActivityInvite;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InviteController extends Controller
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
            $activity_id = $inputs['activity_id'];
            $lists = UserActivityInvite::query()->where('activity_id', $activity_id)
                ->where('parent_user_id', $user_id)
                ->where('has_pay', 1)
                ->get();
            $signal = [];
            $ok = [];
            $no = [];
            foreach ($lists as $list) {
                $user_id = $list->invited_user_id;
                $user = User::getUserById($user_id);
                $user_order = $this->getUserOrder($activity_id, $user_id);
                $user_info = [
                    'logo' => $user->avatar,
                    'name' => $user->nick_name,
                    'buy_name' => $user->name,
                    'pay_time' => $user_order->pay_time,
                ];
                $buy_type = $user_order->type;
                if ($buy_type == 2) {
                    $signal[] = $user_info;
                } else {
                    $group_id = $user_order->group_id;
                    $group = ActivityGroup::query()->find($group_id);
                    if ($group->finished == 1) {
                        $ok[] = $user_info;
                    } else {
                        $no[] = $user_info;
                    }
                }
            }
            $data = [
                'signal' => $signal,
                'ok' => $ok,
                'no' => $no,
            ];
            return self::success($data);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }

    public function getUserOrder($activity_id, $user_id)
    {
        return ActivitySignUser::query()->where('activity_id', $activity_id)->where('user_id', $user_id)
            ->where('status', 3)
            ->orderBy('id', 'desc')
            ->first();
    }
}
