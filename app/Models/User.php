<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public static function getUserById($id)
    {
        return self::query()->find($id);
    }

}
