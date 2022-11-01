<?php


namespace App\Http\Controllers;


use App\Http\Traits\ImageTrait;
use App\Models\BasicSetting;
use Exception;
use Illuminate\Http\Request;


class BasicController extends Controller
{
    use ImageTrait;
    /**
     * @OA\Get(
     *     path="/api/basic/settings",
     *     tags={"基础信息"},
     *     summary="基础配置",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function settings()
    {
        try {
            $info = BasicSetting::query()->find(1);
            $data = [
                'kf_name' => $info->kf_name,
                'mobile' => $info->mobile,
                'pic' => $this->fullImgUrl($info->pic),
                'buy_protocal' => $info->buy_protocal,
                'my_activity_pic'=>$this->fullImgUrl($info->my_activity_pic),
                'my_activity_mobile'=>$info->my_activity_mobile,
            ];
            return self::success($data);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }
}
