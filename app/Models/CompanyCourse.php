<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class CompanyCourse extends Model
{

    protected $table = 'company_course';

    const Type_早教 = 1;
    const Type_水育 = 2;
    const Type_美术 = 3;
    const Type_乐高 = 4;

    const Type_类型列表 = [
        1 => '早教',
        2 => '水育',
        3 => '美术',
        4 => '乐高',
    ];
}
