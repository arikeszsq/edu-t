<?php


namespace App\Http\Controllers;


use App\Models\Activity;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function lists(Request $request)
    {
        try {
            $activities = Activity::query()
                ->where('status', 1)
                ->where('end_time', '>', Carbon::now())
                ->orderBy('id', 'desc')
                ->get();
            return self::success($activities);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }

    public function detail($id)
    {
        try {
            $activity = Activity::query()
                ->where('id', $id)
                ->where('status', 1)
                ->where('end_time', '>', Carbon::now())
                ->firstOrFail();
            return self::success($activity);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }

}
