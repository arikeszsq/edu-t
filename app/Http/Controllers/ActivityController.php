<?php


namespace App\Http\Controllers;


use App\Http\Services\ActivityService;
use Exception;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Integer;

class ActivityController extends Controller
{

    public $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * @OA\Get(
     *     path="/api/activity/lists",
     *     tags={"活动"},
     *     summary="活动列表",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function lists()
    {
        try {
            return self::success($this->activityService->lists());
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/activity/type/{id}",
     *     tags={"活动"},
     *     summary="活动类型",
     *     @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="活动类型 ：1单商家， 2多商家",
     *      @OA\Schema(
     *         type="integer"
     *      )
     *   ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function type($id)
    {
        try {
            return self::success($this->activityService->type($id));
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/activity/detail/{id}",
     *     tags={"活动"},
     *     summary="活动详情",
     *     @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="活动详情",
     *      @OA\Schema(
     *         type="integer"
     *      )
     *   ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function detail($id)
    {
        try {
            $detail = $this->activityService->detail($id);
            return self::success($detail);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }


    /**
     * @OA\Post(
     *     path="/api/activity/invite-user",
     *     tags={"邀请新用户"},
     *     summary="邀请接口 【判断有邀请人的uid ，就调用邀请接口，或者通过分享连接，也调用一次分享接口】",
     *   @OA\RequestBody(
     *       required=true,
     *       description="address edit",
     *       @OA\MediaType(
     *         mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *              @OA\Property(property="activity_id",type="Integer",description="活动的id",),
     *              @OA\Property(property="parent_user_id",type="Integer",description="邀请人的id",),
     *              @OA\Property(property="invited_user_id",type="Integer",description="被邀请人的id",),
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
    public function inviteUser(Request $request)
    {
        $inputs = $request->all();
        $user_id = self::authUserId();
        $inputs['uid'] = $user_id;
        $validator = \Validator::make($inputs, [
            'activity_id' => 'required',
            'parent_user_id' => 'required',
            'invited_user_id' => 'required',
        ], [
            'activity_id.required' => '活动ID必填',
            'parent_user_id.required' => '邀请人ID必填',
            'invited_user_id.required' => '被邀请人ID必填',
        ]);
        if ($validator->fails()) {
            return self::parametersIllegal($validator->messages()->first());
        }
        try {
            return self::success($this->activityService->inviteUser($inputs));
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }
}
