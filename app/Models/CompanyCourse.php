<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompanyCourse extends Model
{

    protected $table = 'company_course';

    protected $fillable = ['type', 'company_id', 'logo_c', 'name', 'price', 'total_num', 'sale_num'];

    public static function getAllCourseByCompanyId($id)
    {
        return CompanyCourse::query()->where('company_id', $id)->get();
    }

    /**
     * @param $activity_id
     * @param $course_id
     * @return int
     * 获取当前活动此课程的报名人数
     */
    public static function getSignNumByCourseId($activity_id, $course_id)
    {
        return ActivitySignUserCourse::query()->where('activity_id', $activity_id)
            ->where('course_id', $course_id)
            ->where('has_pay', 1)
            ->count();
    }

}
