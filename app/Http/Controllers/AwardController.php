<?php


namespace App\Http\Controllers;


use App\Http\Services\ActivityService;
use App\Models\Award;
use App\Models\Company;
use App\Models\UserAward;
use Exception;
use Illuminate\Http\Request;

class AwardController extends Controller
{
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
        $user_id = 1;
        //            $user_id = self::authUserId();
        $inputs['uid'] = $user_id;
        try {
            $list = UserAward::query()
                ->with('activity')
                ->with('award')
                ->where('user_id', $inputs['uid'])
                ->orderBy('id', 'desc')
                ->get();
            return self::success($list);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }


}
