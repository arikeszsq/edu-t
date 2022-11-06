<?php


namespace App\Http\Controllers;


use App\Http\Traits\ImageTrait;
use App\Models\Activity;
use App\Models\ActivitySignCom;
use App\Models\BasicSetting;
use App\Models\CompanyChild;
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


    public function kflist($activity_id)
    {
        $activity = Activity::query()->find($activity_id);
        $data = [];
        if($activity->is_many == Activity::is_many_单商家){
            $info = BasicSetting::query()->find(1);
            $data[]=[
                'name'=>$info->kf_name,
                'mobile' => $info->mobile,
                'pic' => $this->fullImgUrl($info->pic),
                'area' => $info->area,
            ];
        }else{
            $sign_companys = ActivitySignCom::query()->where('activity_id',$activity_id)->get();
            $company_ids = [];
            foreach ($sign_companys as $company)
            {
                $company_ids[] = $company->company_id;
            }
            $schools = CompanyChild::query()->whereIn('company_id',$company_ids)->get();
            foreach ($schools as $school)
            {
                if($school->mobile ||$school->map_area)
                {
                    $data[]=[
                        'name'=>$school->name,
                        'mobile' => $school->mobile,
                        'pic' => $this->fullImgUrl($school->wx_pic),
                        'area' => $school->map_area,
                    ];
                }
            }
        }
        return self::success($data);
    }
}
