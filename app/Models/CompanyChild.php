<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class CompanyChild extends Model
{

    protected $table = 'company_child';

    protected $fillable=['company_id','name','map_area'];

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public static function getAllSchoolsByCompanyId($id)
    {
        return CompanyChild::query()->where('company_id',$id)->get();
    }
}
