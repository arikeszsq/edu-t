<?php

namespace App\Models;


use App\User;
use Illuminate\Database\Eloquent\Model;

class UserViewCount extends Model
{

    protected $table = 'user_view_count';

    public function activity()
    {
        return $this->hasOne(Activity::class, 'id', 'activity_id');
    }

    public function shareUser()
    {
        return $this->hasOne(User::class, 'id', 'share_user_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
