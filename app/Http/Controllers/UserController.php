<?php


namespace App\Http\Controllers;


use App\Http\Traits\ImageTrait;
use App\Http\Traits\MoneyCountTrait;
use App\Http\Traits\WeChatTrait;
use App\Models\Activity;
use App\Models\Share;
use App\Models\UserActivityInvite;
use App\Models\UserAInvitePic;
use App\Models\UserApplyCashOut;
use App\User;
use Illuminate\Http\Request;
use Exception;

class UserController extends Controller
{
    use WeChatTrait;
    use MoneyCountTrait;
    use ImageTrait;

    /**
     * @OA\Get(
     *     path="/api/user/info",
     *     tags={"用户"},
     *     summary="用户信息",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function info(Request $request)
    {
        $inputs = $request->all();
        $user_id = self::authUserId();
        $activity_id = $inputs['activity_id'];
        $user = User::query()->find($user_id);
        try {
            $user_activity_total_money = $this->getAllMoney($user_id, $activity_id);
            $user_activity_cash_out_money = $this->historyCashOutTotalMoney($user_id, $activity_id);
            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'gender' => $user->gender,
                'country' => $user->country,
                'province' => $user->province,
                'city' => $user->city,
                'is_A' => $user->is_A,
                'address' => $user->address,
                'map_points' => $user->map_points,
                'user_activity_total_money' => $user_activity_total_money,//总奖励
                'user_activity_cash_out_money' => $user_activity_cash_out_money,//总提取
                'current_stay_money' => ($user_activity_total_money) - ($user_activity_cash_out_money),//账户余额
                'history_total_money' => $user_activity_cash_out_money,
                'share_num' => Share::shareNum($user_id, $activity_id) ?: 0,
                'share_success_num' => UserActivityInvite::getUserInviteSuccessNum($activity_id, $user_id) ?: 0,
            ];
            return self::success($data);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }


    /**
     * @OA\Post(
     *     path="/api/user/update",
     *     tags={"用户"},
     *     summary="设置家的位置",
     *   @OA\RequestBody(
     *       required=true,
     *       description="设置家的位置",
     *       @OA\MediaType(
     *         mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *              @OA\Property(property="map_points",type="String",description="设置家的地址，纬经度"),
     *              @OA\Property(property="address",type="String",description="设置家的地址"),
     *          ),
     *       ),
     *   ),
     *     @OA\Response(
     *         response=100000,
     *         description="success"
     *     )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function update(Request $request)
    {
        $inputs = $request->all();
        $user_id = self::authUserId();
        $inputs['uid'] = $user_id;
        $user = User::query()->find($user_id);
        try {
            if (isset($inputs['map_points']) && $inputs['map_points']) {
                $user->map_points = $inputs['map_points'];
            }
            if (isset($inputs['address']) && $inputs['address']) {
                $user->address = $inputs['address'];
            }
            if (isset($inputs['nickname']) && $inputs['nickname']) {
                $user->nick_name = $inputs['nickname'];
            }
            if (isset($inputs['avatarUrl']) && $inputs['avatarUrl']) {
                $user->avatar = $inputs['avatarUrl'];
            }
            $user->updated_at = date('Y-m-d H:i:s', time());
            $user->save();
            return self::success($user);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 设置用户为A用户
     */
    public function setA()
    {
        $user_id = self::authUserId();
        try {
            $user = User::query()->find($user_id);
            $user->is_A = 1;
            $user->save();
//            $a_invite_user = UserAInvitePic::query()->orderBy('id', 'desc')->first();
//            $activity = Activity::query()->find($a_invite_user->activity_id);
            $data = [
//                'type' => $activity->is_many,
//                'activity_id' => $activity->id,
                'name' => $user->name
            ];
            return self::success($data);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }

    public function getInvitePic(Request $request)
    {
        $inputs = $request->all();
        $user_id = self::authUserId();
        $activity_id = $inputs['activity_id'];
        $pic_url = $this->getUserInvitePic($activity_id, $user_id);
        return self::success($this->fullImgUrl($pic_url));
    }

    public function applyCashOut(Request $request)
    {
        $inputs = $request->all();
        $activity_id = $inputs['activity_id'];
        $user_id = self::authUserId();
        $apply_money = $inputs['apply_money'];
        $user_total_money = $this->getAllMoney($user_id, $activity_id);
        $history_out_money = $this->historyCashOutTotalMoney($user_id, $activity_id);
        $current_money = $user_total_money - $history_out_money;

        if ($apply_money <= $current_money) {
            $data = [
                'user_id' => $user_id,
                'apply_money' => $apply_money,
                'history_total_money' => $history_out_money,
                'current_stay_money' => $current_money,
                'created_at' => date('Y-m-d H:i:s', time())
            ];
            $id = UserApplyCashOut::query()->insertGetId($data);
            return self::success($id);
        } else {
            return self::error(10001, '余额不足');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     * 小程序转发时，获取活动名，转发图，转发人等信息
     */
    public function shareInfo(Request $request)
    {
        $inputs = $request->all();
        $user_id = self::authUserId();
        $inputs['uid'] = $user_id;
        $user = User::query()->find($user_id);
        $activity = Activity::query()->find($inputs['activity_id']);
        try {
            $data = [
                'user_id' => $user->id,
                'bg' => $activity->mini_bg,
                'title' => $activity->title
            ];
            return self::success($data);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }
}
