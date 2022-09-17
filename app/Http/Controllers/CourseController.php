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

    /**
     * @OA\Get(
     *     path="/api/course/type-lists",
     *     tags={"课程"},
     *     summary="课程分类",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function typeLists()
    {
        return self::success($this->courseService->lists(CompanyCourse::Type_类型列表));
    }

    /**
     * @OA\Get(
     *     path="/api/course/lists",
     *     tags={"课程"},
     *     summary="课程列表",
     *     @OA\Parameter(name="activity_id",in="query",description="活动id",required=true),
     *     @OA\Parameter(name="type",in="query",description="课程类型：1 => '早教',2 => '水育',3 => '美术',4 => '乐高',5 => '围棋',6 => '硬笔',7 => '软笔',8 => '国画',"),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
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

    /**
     * @OA\Get(
     *     path="/api/course/detail/{id}",
     *     tags={"课程"},
     *     summary="课程详情",
     *     @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="课程详情",
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
            return self::success(CompanyCourse::query()->find($id));
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/course/company-child-lists/{id}",
     *     tags={"课程"},
     *     summary="课程校区列表",
     *     @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="课程Id",
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
    public function companyChildList($course_id)
    {
        try {
            return self::success($this->courseService->companyChildList($course_id));
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }
}
