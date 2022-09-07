<?php

namespace App\Http\Services;

use App\Exceptions\ObjectNotExistException;
use App\Http\Traits\ImageTrait;
use App\Models\Activity;
use App\Models\ActivityGroup;
use App\Models\ActivitySignCom;
use App\Models\ActivitySignUser;
use App\Models\CompanyChild;
use App\Models\CompanyCourse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CourseService
{
    use ImageTrait;

    public function create($inputs)
    {
        $course_ids = $inputs['course_ids'];
        $school_child_ids = $inputs['school_child_ids'];
        $course_ids_array = explode(',', $course_ids);
        $school_child_ids_array = explode(',', $school_child_ids);
        $activity_sign_user_course = [];
        foreach ($course_ids_array as $key => $value) {
            $activity_sign_user_course[] = [
                'activity_id' => $inputs['activity_id'],
                'school_id' => $school_child_ids_array[$key],
                'course_id' => $value,
                'user_id' => $inputs['uid'],
            ];
        }
        return DB::table('user_activity_invite')->insert($activity_sign_user_course);
    }


    public function lists($inputs)
    {
        $activity_id = $inputs['activity_id'];

        $query = DB::table('activity_sign_com as com')
            ->leftJoin('company_course as course', 'com.company_id', 'course.company_id')
            ->leftJoin('company', 'course.company_id', 'company.id')
            ->select(['course.id as id', 'course.logo', 'course.name', 'course.price', 'course.total_num', 'course.sale_num',
                'company.name as company_name'])
            ->where('com.activity_id', $activity_id);
        if (isset($inputs['type']) && $inputs['type']) {
            $query->where('course.type', $inputs['type']);
        }
        $lists = $query->get();

        $course = [];
        foreach ($lists as $list) {
            $course[] = [
                'id' => $list->id,
                'logo' => $this->fullImgUrl($list->logo),
                'name' => $list->name,
                'price' => $list->price,
                'total_num' => $list->total_num,
                'sale_num' => $list->sale_num,
                'company_name' => $list->company_name,
                'gap' => '0km'
            ];
        }

        return $course;
    }


    public function companyChildList($course_id)
    {
        $course = CompanyCourse::query()->find($course_id);
        $children = CompanyChild::query()->where('company_id', $course->company_id)->get();
        $data = [];
        foreach ($children as $child) {

            $gap = 0;

            $data[] = [
                'id' => $child->id,
                'name' => $child->name,
                'address' => $child->address,
                'gap' => $gap
            ];
        }
        return $data;
    }

}
