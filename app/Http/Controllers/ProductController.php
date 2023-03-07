<?php


namespace App\Http\Controllers;


use App\Models\ActivityGroup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function list()
    {
        return view('web.product.list');
    }
    public function detail($id)
    {
        return view('web.product.detail');
    }
}
