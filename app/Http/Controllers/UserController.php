<?php


namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\Request;
use Exception;

class UserController extends Controller
{
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
    public function info()
    {
        $user_id = self::authUserId();
        $inputs['uid'] = $user_id;
        $user = User::query()->find($user_id);
        try {
            $data = [
                'name' => $user->nickName,
                'avatar' => $user->avatarUrl,
                'gender' => $user->gender,
                'country' => $user->country,
                'province' => $user->province,
                'city' => $user->city,
                'is_A' => $user->is_A,
                'address' => $user->address,
                'map_points' => $user->map_points,
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
            return self::success($user);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }
}
