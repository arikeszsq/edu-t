<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\ShowUser;
use App\Admin\Metrics\Examples;
use App\Admin\Renderable\CompanyList;
use App\Admin\Renderable\UserTable;
use App\Http\Controllers\Controller;
use App\Http\Traits\WeChatTrait;
use App\Models\ActivitySignUser;
use App\Models\ActivityViewLog;
use App\Models\User;
use Dcat\Admin\Http\Controllers\Dashboard;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;

use Dcat\Admin\Widgets\Modal;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Http\StreamResponse;
use Illuminate\Support\Facades\Log;


class HomeController extends Controller
{

    use WeChatTrait;

    /**
     * @param Content $content
     * @return mixed
     *
     *         解决方案
     * 路径问题
     * 传入的页面路径，pages前不可用加 " / "
     * 正确：‘pages/index/index’
     * 错误：‘/pages/index/index’
     *
     * 小程序参数问题
     * 小程序的参数不能超过32个字符
     *
     *          小程序是否发布
     * 传入page，生成指定页面的二维码的前提是，小程序必须审核并发布
     * 审核成功并发布的小程序才能正常调用二维码生成接口
     */
//    public function index(Content $content)
//    {
//
//
////        $access_token = $this->getShareQCode(3, 1);
////
////        var_dump($access_token);
////        exit;
//
////        return $content->body(UserTable::make());
//
//        $modal = Modal::make()
//            ->lg()
//            ->title('异步加载 - 表格')
//            ->body(CompanyList::make()) // Modal 组件支持直接传递 渲染类实例
//            ->button('打开表格');
//
//        return $content->body($modal);
//
//
//    }


    public function index(Content $content)
    {
        $total_user_num = User::query()->count();
        $total_user_has_buy = ActivitySignUser::query()->where('has_pay', 1)
            ->groupBy('user_id')
            ->count();

        $total_money = ActivitySignUser::query()->where('has_pay', 1)->sum('money');

        $total_user_has_buy_today = ActivitySignUser::query()->where('has_pay', 1)
            ->where('created_at', '>=', date('Y-m-d 00:00:00', time()))
            ->where('created_at', '<=', date('Y-m-d 23:59:00', time()))
            ->groupBy('user_id')
            ->count();

        $view_num_today = ActivityViewLog::query()->where('created_at', '>=', date('Y-m-d 00:00:00', time()))
            ->where('created_at', '<=', date('Y-m-d 23:59:00', time()))->count();
        $sign_num_today = ActivitySignUser::query()->where('created_at', '>=', date('Y-m-d 00:00:00', time()))
            ->where('created_at', '<=', date('Y-m-d 23:59:00', time()))->where('has_pay', 1)->count();
        $share_num_today = ActivitySignUser::query()->where('created_at', '>=', date('Y-m-d 00:00:00', time()))
            ->where('created_at', '<=', date('Y-m-d 23:59:00', time()))->count();
        $sale_num_today = ActivitySignUser::query()->where('created_at', '>=', date('Y-m-d 00:00:00', time()))
            ->where('created_at', '<=', date('Y-m-d 23:59:00', time()))->where('has_pay', 1)->sum('money');


        $view_num = ActivityViewLog::query()->count();
        $sign_num = ActivitySignUser::query()->where('has_pay', 1)->count();
        $share_num = ActivitySignUser::query()->count();
        $sale_num = ActivitySignUser::query()->where('has_pay', 1)->sum('money');


        $yesterday_view = ActivityViewLog::query()
            ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime('-1 day')))
            ->where('created_at', '<=', date('Y-m-d 23:59:00', strtotime('-1 day')))->count();
        $yesterday_share = ActivitySignUser::query()
            ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime('-1 day')))
            ->where('created_at', '<=', date('Y-m-d 23:59:00', strtotime('-1 day')))->count();
        $yesterday_sign = ActivitySignUser::query()->where('has_pay', 1)
            ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime('-1 day')))
            ->where('created_at', '<=', date('Y-m-d 23:59:00', strtotime('-1 day')))->count();
        $yesterday_pay = ActivitySignUser::query()->where('has_pay', 1)
            ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime('-1 day')))
            ->where('created_at', '<=', date('Y-m-d 23:59:00', strtotime('-1 day')))->sum('money');

        $today_view = ActivityViewLog::query()
            ->where('created_at', '>=', date('Y-m-d 00:00:00', time()))
            ->where('created_at', '<=', date('Y-m-d 23:59:00', time()))->count();
        $today_share = ActivitySignUser::query()
            ->where('created_at', '>=', date('Y-m-d 00:00:00', time()))
            ->where('created_at', '<=', date('Y-m-d 23:59:00', time()))->count();
        $today_sign = ActivitySignUser::query()->where('has_pay', 1)
            ->where('created_at', '>=', date('Y-m-d 00:00:00', time()))
            ->where('created_at', '<=', date('Y-m-d 23:59:00', time()))->count();
        $today_pay = ActivitySignUser::query()->where('has_pay', 1)
            ->where('created_at', '>=', date('Y-m-d 00:00:00', time()))
            ->where('created_at', '<=', date('Y-m-d 23:59:00', time()))->sum('money');

        $total_view = ActivityViewLog::query()->count();
        $total_share = ActivitySignUser::query()->count();
        $total_sign = ActivitySignUser::query()->where('has_pay', 1)->count();
        $total_pay = ActivitySignUser::query()->where('has_pay', 1)->sum('money');

        $data = [
            //客户统计
            'total_money' => $total_money,//总资产
            'total_user' => $total_user_num,//客户总数
            'total_pay_user_num' => $total_user_has_buy,//下单客户总数

            //传播统计
            'total_user_today_view' => $view_num_today,//今日浏览人数
            'total_user_today_sign' => $sign_num_today,//今日报名人数
            'total_user_today_share' => $share_num_today,//今日分享人数
            'total_user_today_pay' => $sale_num_today,//今日付款金额
            'total_user_view' => $view_num,//总浏览人数
            'total_user_sign' => $sign_num,//总报名人数
            'total_user_share' => $share_num,//总分享人数
            'total_user_pay' => $sale_num,//总付款金额

            //成交线索统计
            'total_user_today_new' => $total_user_has_buy_today,//成交 - 今日新增
            'total_user_num' => $total_user_has_buy,// 成交 - 总人数

            //总活动数据
            'yesterday_view' => $yesterday_view,//昨日-访问量
            'yesterday_share' => $yesterday_share,//昨日-分享量
            'yesterday_sign' => $yesterday_sign,//昨日-报名量
            'yesterday_sale' => $yesterday_pay,//昨日-销售金额
            'today_view' => $today_view,//今日-访问量
            'today_share' => $today_share,//今日-分享量
            'today_sign' => $today_sign,//今日-报名量
            'today_sale' => $today_pay,//今日-销售金额
            'total_view' => $total_view,//总-访问量
            'total_share' => $total_share,//总-分享量
            'total_sign' => $total_sign,//总-报名量
            'total_sale' => $total_pay,//总-销售金额
        ];

        return $content->body(view('home', $data));

//        return $content
//            ->header('首页概览')
//            ->description('数据分析')
//            ->body(function (Row $row) {
//
//                $row->column(12, function (Column $column) {
//                    $column->row(function (Row $row) {
//                        $row->column(12, new Examples\ProductOrders());
//                    });
//
//                    $column->row(function (Row $row) {
//                        $row->column(6, new Examples\DealCount());
//                        $row->column(6, new Examples\TotalCount());
//                    });
//                    $column->row(new Examples\ShareCount());
//                });
//            });
    }
}
