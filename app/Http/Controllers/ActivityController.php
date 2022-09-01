<?php


namespace App\Http\Controllers;


use App\Http\Services\ActivityService;
use Exception;
use Illuminate\Http\Request;

class ActivityController extends Controller
{

    public $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    public function lists(Request $request)
    {
        try {
            return self::success($this->activityService->lists());
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }

    public function type($id)
    {
        try {
            return self::success($this->activityService->type($id));
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }

    public function detail($id)
    {
        try {
            $detail = $this->activityService->detail($id);
            return self::success($detail);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }
}
