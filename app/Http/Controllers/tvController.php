<?php


namespace App\Http\Controllers;


use App\Models\ActivityGroup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class tvController extends Controller
{
    public function index()
    {
        $data = [];
        return view('tv', $data);
    }
}
