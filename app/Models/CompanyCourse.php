<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompanyCourse extends Model
{

    protected $table = 'company_course';

    protected $fillable = ['type', 'company_id', 'logo_c', 'name', 'price', 'total_num', 'sale_num'];

}
