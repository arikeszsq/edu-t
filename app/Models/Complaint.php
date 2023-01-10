<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{

    protected $table = 'complaint';

    public function activity()
    {
        return $this->hasOne(Activity::class, 'id', 'activity_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
