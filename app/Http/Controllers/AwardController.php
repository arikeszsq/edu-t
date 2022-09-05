<?php

namespace App\Http\Controllers;

use App\Models\ActivityGroup;
use App\Models\ActivitySignUser;
use App\Models\Award;
use App\Models\UserAward;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AwardController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function create(Request $request)
    {
        $inputs = $request->all();
        $user_id = self::authUserId();
        $inputs['uid'] = $user_id;
        $validator = \Validator::make($inputs, [
            'activity_id' => 'required',
            'id' => 'required',
        ], [
            'activity_id.required' => '活动ID必填',
            'id.required' => '奖励ID必填',
        ]);
        if ($validator->fails()) {
            return self::parametersIllegal($validator->messages()->first());
        }
        $first = UserAward::query()->where('user_id', $inputs['uid'])
            ->where('activity_id', $inputs['activity_id'])
            ->where('award_id', $inputs['id'])
            ->first();
        if ($first) {
            return self::error(10001, '请勿重复领取奖励');
        }
        try {
            $award = Award::query()->find($inputs['id']);
            if ($award->is_free != 1) {
                if ($award->is_commander == 1) {
                    $is_commander = ActivityGroup::query()->where('leader_id', $inputs['uid'])->where('activity_id', $inputs['activity_id'])
                        ->where('status', ActivityGroup::Status_有效_已支付)->first();
                    if (!$is_commander) {
                        return self::error(10002, '只有团长才可以领取');
                    }
                }

                if ($award->group_ok == 1) {
                    $is_group_ok = ActivitySignUser::query()->where('activity_id', $inputs['activity_id'])
                        ->with('group')
                        ->where('user_id', $inputs['uid'])
                        ->where('has_pay', ActivitySignUser::Status_已支付)
                        ->first();
                    if (!$is_group_ok || $is_group_ok->group->finished == 2) {
                        return self::error(10002, '成团后才可以领取');
                    }
                }

                if ($award->invite_num > 0) {
                    $user_invite_num = DB::table('user_activity_invite')->where('activity_id',$inputs['activity_id'])
                    ->where(function($query) use($inputs){
                        $query->where('A_user_id',$inputs['uid'])->orWhere('parent_user_id',$inputs['uid']);
                    });
                    $num = $user_invite_num->count();
                    if($num<$award->invite_num){
                        return self::error(10003, '邀请人数不足，无法领取');
                    }
                }
            }
            $user_award = $this->addNewUserAward($inputs);
            return self::success($user_award);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }

    public function addNewUserAward($inputs)
    {
        return UserAward::query()->insert([
            'activity_id' => $inputs['activity_id'],
            'user_id' => $inputs['uid'],
            'award_id' => $inputs['id']
        ]);
    }


    public function lists(Request $request)
    {
        try {
            $list = Award::query()
                ->where('status', Award::Status_有效)
                ->orderBy('id', 'desc')
                ->get();
            return self::success($list);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }


    public function myLists(Request $request)
    {
//        $inputs = $request->all();
//        $validator = \Validator::make($inputs, [
//            'activity_id' => 'required',
//        ], [
//            'activity_id.required' => '活动ID必填',
//        ]);
//        if ($validator->fails()) {
//            return self::parametersIllegal($validator->messages()->first());
//        }
        $user_id = self::authUserId();
        $inputs['uid'] = $user_id;
        try {
            $list = UserAward::query()
                ->with('activity')
                ->with('award')
//                ->where('activity_id', $inputs['activity_id'])
                ->where('user_id', $inputs['uid'])
                ->orderBy('id', 'desc')
                ->get();
            return self::success($list);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }


}