<?php

namespace App\Admin\Metrics\Examples;

use App\Models\ActivitySignUser;
use App\Models\User;
use Dcat\Admin\Widgets\Metrics\Round;
use Illuminate\Http\Request;

class DealCount extends Round
{
    /**
     * 初始化卡片内容
     */
    protected function init()
    {
        parent::init();

        $this->title('成交线索统计');
        $this->chartLabels(['今日新增', '总人数']);
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
        $total_user_has_buy_today= ActivitySignUser::query()->where('has_pay', 1)
            ->where('created_at','>=',date('Y-m-d 00:00:00',time()))
            ->where('created_at','<=',date('Y-m-d 23:59:00',time()))
            ->groupBy('user_id')
            ->count();
        $total_user_has_buy = ActivitySignUser::query()->where('has_pay', 1)->groupBy('user_id')->count();
        switch ($request->get('option')) {
            default:
                // 卡片内容
                $this->withContent($total_user_has_buy_today, $total_user_has_buy);
                // 图表数据
                $this->withChart([$total_user_has_buy_today, $total_user_has_buy]);
                // 总数
                $this->chartTotal('成交总人数', $total_user_has_buy);
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

    /**
     * 卡片内容.
     *
     * @param int $finished
     * @param int $pending
     *
     * @return $this
     */
    public function withContent($finished, $pending)
    {
        return $this->content(
            <<<HTML
<div class="col-12 d-flex flex-column flex-wrap text-center" style="max-width: 220px">
    <div class="chart-info d-flex justify-content-between mb-1 mt-2" >
          <div class="series-info d-flex align-items-center">
              <i class="fa fa-circle-o text-bold-700 text-primary"></i>
              <span class="text-bold-600 ml-50">总资产</span>
          </div>
          <div class="product-result">
              <span>{$finished}</span>
          </div>
    </div>

    <div class="chart-info d-flex justify-content-between mb-1">
          <div class="series-info d-flex align-items-center">
              <i class="fa fa-circle-o text-bold-700 text-warning"></i>
              <span class="text-bold-600 ml-50">客户总数</span>
          </div>
          <div class="product-result">
              <span>{$pending}</span>
          </div>
    </div>
</div>
HTML
        );
    }
}
