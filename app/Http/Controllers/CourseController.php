<?php


namespace App\Http\Controllers;

use App\Http\Services\CourseService;
use App\Models\CompanyCourse;
use Exception;
use Illuminate\Http\Request;

class CourseController extends Controller
{

    public $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function typeLists()
    {
        return CompanyCourse::Type_类型列表;
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
            return self::success($this->courseService->lists($inputs));
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }


    public function companyChildList($course_id)
    {
        try {
            return self::success($this->courseService->companyChildList($course_id));
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }
}
