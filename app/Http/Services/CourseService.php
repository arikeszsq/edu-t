<?php

namespace App\Http\Services;

use App\Exceptions\ObjectNotExistException;
use App\Http\Traits\ImageTrait;
use App\Models\Activity;
use App\Models\ActivityGroup;
use App\Models\ActivitySignCom;
use App\Models\ActivitySignUser;
use App\Models\CompanyCourse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CourseService
{
    use ImageTrait;
    public function lists($inputs)
    {
        $list = [];
        $activity_id = $inputs['activity_id'];

        $query = DB::table('activity_sign_com as com')
            ->leftJoin('company_course as course', 'com.company_id', 'course.company_id')
            ->leftJoin('company','course.company_id','company.id')
            ->select(['course.id as id','course.logo','course.name','course.price','course.total_num','course.sale_num',
                'company.name as company_name'])
            ->where('com.activity_id', $activity_id);
        if (isset($inputs['type']) && $inputs['type']) {
            $query->where('course.type', $inputs['type']);
        }
        $lists = $query->get();

        $course =[];
        foreach ($lists as $list) {
            $course[] = [
                'id'=>$list->id,
                'logo' => $this->fullImgUrl($list->logo),
                'name' => $list->name,
                'price' =>$list->price,
                'total_num' => $list->total_num,
                'sale_num' => $list->sale_num,
                'company_name' =>$list->company_name,
            ];
        }

        return $course;
    }

}
