<?php


namespace App\Http\Controllers;


use App\Models\ActivityGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index()
    {
        $data = [];
        return view('mobile.index', $data);
    }

    public function addContent(Request $request)
    {
        $inputs = $request->all();
        $type = $inputs['type'];
        $content = $inputs['content'];
        $ret = DB::table('zsq_content')->insert([
            'type' => $type,
            'content' => $content,
            'created_at' => Carbon::now()
        ]);
        if ($ret) {
            return [
                'code' => 200,
            ];
        } else {
            return [
                'code' => 10001,
            ];
        }
    }
}
