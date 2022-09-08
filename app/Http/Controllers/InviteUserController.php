<?php


namespace App\Http\Controllers;

use App\Models\UserActivityInvite;
use Exception;
use App\User;
use Illuminate\Http\Request;

class InviteUserController extends Controller
{
    public function create(Request $request)
    {
        $inputs = $request->all();
        $user_id = self::authUserId();
        $inputs['uid'] = $user_id;
        $validator = \Validator::make($inputs, [
            'activity_id' => 'required',
            'parent_uid' => 'required',
            'invite_uid' => 'required',
        ], [
            'activity_id.required' => '活动ID必填',
            'parent_uid.required' => '邀请人ID必填',
            'invite_uid.required' => '被邀请人ID必填',
        ]);
        if ($validator->fails()) {
            return self::parametersIllegal($validator->messages()->first());
        }
        try {
            $a_user_id = User::getAUidByUid($inputs['parent_uid']);
            $invite = UserActivityInvite::query()->insertGetId([
                'activity_id' => $inputs['activity_id'],
                'A_user_id' => $a_user_id,
                'parent_user_id' => $inputs['parent_uid'],
                'invited_user_id' => $inputs['invite_uid'],
                'has_pay' => 1,
            ]);
            return self::success($invite);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }
}
