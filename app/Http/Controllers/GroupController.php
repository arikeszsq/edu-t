<?php


namespace App\Http\Controllers;


use App\Http\Services\ActivityService;
use App\Http\Services\GroupService;
use Exception;
use Illuminate\Http\Request;

class GroupController extends Controller
{

    public $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function create(Request $request)
    {
        //支付成功之后才会加团或者开团，方法放在ActivityGroup的模型里
    }


    public function lists(Request $request)
    {
        $inputs = $request->all();
        $validator = \Validator::make($inputs, [
            'activity_id' => 'required',
        ], [
            'activity_id.required' => '活动ID必填',
        ]);
        if ($validator->fails()) {
            return self::parametersIllegal($validator->messages()->first());
        }
        try {
            return self::success($this->groupService->lists($inputs));
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }

    public function userList($id)
    {
        try {
            return self::success($this->groupService->userList($id));
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }
}
