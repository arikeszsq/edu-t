<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class CompanyCourseType extends Model
{

    protected $table = 'company_course_type';

    public static function getListArray()
    {
        $list = CompanyCourseType::query()->orderBy('id','asc')->get();
        $data = [];
        foreach ($list as $type)
        {
            $data[$type->id]=$type->name;
        }
        return $data;
    }

}
