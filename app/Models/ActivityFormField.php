<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ActivityFormField extends Model
{

    protected $table = 'activity_form_fields';


    public function options()
    {
        return $this->hasMany(ActivityFormFieldOption::class, 'activity_form_id', 'id');
    }

    protected $fillable=['name'];
}
