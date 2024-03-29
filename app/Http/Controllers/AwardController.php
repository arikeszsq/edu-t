<?php

namespace App\Http\Controllers;

use App\Http\Traits\ImageTrait;
use App\Models\ActivityGroup;
use App\Models\ActivitySignUser;
use App\Models\Award;
use App\Models\UserActivityInvite;
use App\Models\UserAward;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AwardController extends Controller
{

    use ImageTrait;

    /**
     * 领取奖励
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
            return self::error(10002, '请勿重复领取奖励');
        }
        try {
            $ret = Award::isUserCanGet($inputs['id'], $user_id);
            if ($ret == 200) {
                $user_award = UserAward::query()->insert([
                    'activity_id' => $inputs['activity_id'],
                    'user_id' => $inputs['uid'],
                    'award_id' => $inputs['id'],
                    'created_at' => Carbon::now()
                ]);
                return self::success($user_award);
            } else {
                return self::error(10001, '失败');
            }
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }


    /**
     * @OA\Get(
     *     path="/api/award/lists",
     *     tags={"奖励"},
     *     summary="奖励列表",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function lists(Request $request)
    {
        $inputs = $request->all();
        $user_id = self::authUserId();
        $activity_id = $inputs['activity_id'];
        try {
            $list = Award::query()
                ->where('status', Award::Status_有效)
                ->where('activity_id', $inputs['activity_id'])
                ->orderBy('id', 'desc')
                ->get();
            $data = [];
            $user_invite_num = UserActivityInvite::getUserInviteSuccessNum($inputs['activity_id'], $user_id);
            foreach ($list as $award) {
                $has_get = UserAward::hasGetAlready($activity_id, $user_id, $award->id);
                $can_user_get = Award::isUserCanGet($award->id, $user_id);
                if ($can_user_get == 200) {
                    $can_get = 1;
                } else {
                    $can_get = 2;
                }
                $data[] = [
                    'id' => $award->id,
                    'name' => $award->name,
                    'short_name' => $award->short_name,
                    'logo' => $this->fullImgUrl($award->logo),
                    'description' => $award->description,
                    'invite_num' => $award->invite_num,
                    'status' => $award->status,
                    'created_at' => $award->created_at,
                    'price' => $award->price,
                    'is_commander' => $award->is_commander,
                    'group_ok' => $award->group_ok,
                    'is_free' => $award->is_free,
                    'can_get' => $award->is_free == 1 ? 1 : $can_get,
                    'user_invite_num' => $user_invite_num,
                    'has_get' => $has_get
                ];
            }
            return self::success($data);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }


    /**
     * 选择课程时，可以选择的奖励
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function onlyLists(Request $request)
    {
        $inputs = $request->all();
        $activity_id = $inputs['activity_id'];
        try {
            $list = Award::query()
                ->where('status', Award::Status_有效)
                ->where('activity_id', $activity_id)
                ->orderBy('id', 'desc')
                ->get();
            $data = [];
            foreach ($list as $award) {
                $data[] = [
                    'id' => $award->id,
                    'title' => $award->name,
                    'short_name' => $award->short_name,
                    'imgUrl' => $this->fullImgUrl($award->logo),
                    'description' => $award->description,
                    'invite_num' => $award->invite_num,
                    'status' => $award->status,
                    'created_at' => $award->created_at,
                    'price' => $award->price,
                    'is_commander' => $award->is_commander,
                    'group_ok' => $award->group_ok,
                    'is_free' => $award->is_free,
                    'checked' => false
                ];
            }
            return self::success($data);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     * 我的奖励
     */
    public function myLists(Request $request)
    {
        $inputs = $request->all();
        $user_id = self::authUserId();
        $inputs['uid'] = $user_id;
        try {
            $list = UserAward::query()
                ->with('activity')
                ->with('award')
                ->where('activity_id', $inputs['activity_id'])
                ->where('user_id', $inputs['uid'])
                ->orderBy('id', 'desc')
                ->get();
            $data = [];
            foreach ($list as $award) {
                $data[] = [
                    'name' => $award->award->name,
                    'short_name' => $award->award->short_name,
                    'logo' => $this->fullImgUrl($award->award->logo),
                    'description' => $award->award->description
                ];
            }
            return self::success($data);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }


}
