<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\ShowUser;
use App\Admin\Metrics\Examples;
use App\Http\Controllers\Controller;
use App\Http\Traits\WeChatTrait;
use Dcat\Admin\Http\Controllers\Dashboard;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;

use EasyWeChat\Factory;
use EasyWeChat\Kernel\Http\StreamResponse;
use Illuminate\Support\Facades\Log;


class HomeController extends Controller
{

    use WeChatTrait;
    public function index(Content $content)
    {
        $access_token = $this->getShareQCode(3);
//        var_dump($access_token);exit;
        return $content->body(ShowUser::make());
    }




//    public function index(Content $content)
//    {
//        return $content
//            ->header('Dashboard')
//            ->description('Description...')
//            ->body(function (Row $row) {
//                $row->column(6, function (Column $column) {
//                    $column->row(Dashboard::title());
//                    $column->row(new Examples\Tickets());
//                });
//
//                $row->column(6, function (Column $column) {
//                    $column->row(function (Row $row) {
//                        $row->column(6, new Examples\NewUsers());
//                        $row->column(6, new Examples\NewDevices());
//                    });
//
//                    $column->row(new Examples\Sessions());
//                    $column->row(new Examples\ProductOrders());
//                });
//            });
//    }
}
