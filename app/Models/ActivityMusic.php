<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ActivityMusic extends Model
{

    protected $table = 'activity_music';
    public $timestamps = false;


    public static  function getMusicArray()
    {
        $lists = ActivityMusic::query()->get();
        $data = [];
        foreach ($lists as $list){
            $data[$list->id]=$list->name;
        }
        return $data;
    }

}
