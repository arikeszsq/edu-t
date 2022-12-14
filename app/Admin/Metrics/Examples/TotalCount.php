<?php

namespace App\Admin\Metrics\Examples;

use App\Models\ActivitySignUser;
use App\Models\ActivityViewLog;
use App\Models\User;
use Dcat\Admin\Widgets\Metrics\Round;
use Illuminate\Http\Request;

class TotalCount extends Round
{
    /**
     * 初始化卡片内容
     */
    protected function init()
    {
        parent::init();

        $this->title('总活动数据');
        $this->chartLabels(['访问量', '分享量', '报名量', '销售金额']);
    }

    /**
     * 处理请求
     *
     * @param Request $request
     *
     * @return mixed|void
     */
    public function handle(Request $request)
    {
        $view_num = ActivityViewLog::query()->count();
        $share_num = ActivitySignUser::query()->count();
        $sign_num = ActivitySignUser::query()->where('has_pay', 1)->count();
        $sale_num = ActivitySignUser::query()->where('has_pay', 1)->sum('money');
        switch ($request->get('option')) {
            default:
                // 卡片内容
                $this->withContent($view_num, $share_num,$sign_num, $sale_num);
        }
    }

    /**
     * 设置图表数据.
     *
     * @param array $data
     *
     * @return $this
     */
    public function withChart(array $data)
    {
        return $this->chart([
            'series' => $data,
        ]);
    }

    public function withContent($view_num, $share_num,$sign_num, $sale_num)
    {
        return $this->content(
            <<<HTML
<div class="col-12 d-flex flex-column flex-wrap text-center" style="max-width: 220px">
    <div class="chart-info d-flex justify-content-between mb-1 mt-2" >
          <div class="series-info d-flex align-items-center">
              <i class="fa fa-circle-o text-bold-700 text-primary"></i>
              <span class="text-bold-600 ml-50">访问量</span>
          </div>
          <div class="product-result">
              <span>{$view_num}</span>
          </div>
    </div>

    <div class="chart-info d-flex justify-content-between mb-1">
          <div class="series-info d-flex align-items-center">
              <i class="fa fa-circle-o text-bold-700 text-warning"></i>
              <span class="text-bold-600 ml-50">分享量</span>
          </div>
          <div class="product-result">
              <span>{$share_num}</span>
          </div>
    </div>

     <div class="chart-info d-flex justify-content-between mb-1">
          <div class="series-info d-flex align-items-center">
              <i class="fa fa-circle-o text-bold-700 text-danger"></i>
              <span class="text-bold-600 ml-50">报名量</span>
          </div>
          <div class="product-result">
              <span>{$sign_num}</span>
          </div>
    </div>
    <div class="chart-info d-flex justify-content-between mb-1">
          <div class="series-info d-flex align-items-center">
              <i class="fa fa-circle-o text-bold-700 text-danger"></i>
              <span class="text-bold-600 ml-50">销售金额</span>
          </div>
          <div class="product-result">
              <span>{$sale_num}</span>
          </div>
    </div>
</div>
HTML
        );
    }
}
