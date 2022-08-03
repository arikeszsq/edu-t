<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ActivitySignCom extends Model
{

    protected $table = 'activity_sign_com';


    public function company()
    {
        return $this->hasOne(Company::class,'id','company_id');
    }
}
