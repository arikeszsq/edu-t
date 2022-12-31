<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ActivityFormFieldOption extends Model
{

    protected $table = 'activity_form_field_options';

    protected $fillable=['name'];

    public function formfield()
    {
        return $this->belongsTo(ActivityFormField::class, 'id','activity_form_id');
    }

}
