<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Dcat\Admin\Layout\Content;

class ActivityNewController extends Controller
{
    public function index(Content $content)
    {
        $type = request()->get('type');
        if ($type == 1) {
            $data = ['type' => 1, 'name' => '单商家活动'];
        } else {
            $data = ['type' => 2, 'name' => '多商家活动'];
        }
        return $content->body(view('activity', $data));
    }
}
