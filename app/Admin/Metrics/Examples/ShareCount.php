<?php

namespace App\Admin\Metrics\Examples;

use App\Models\ActivitySignUser;
use App\Models\User;
use Dcat\Admin\Widgets\Metrics\Round;
use Illuminate\Http\Request;

class ShareCount extends Round
{
    /**
     * 初始化卡片内容
     */
    protected function init()
    {
        parent::init();

        $this->title('传播统计');
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
        $this->withContent();
//        switch ($request->get('option')) {
//            default:
//                // 卡片内容
//                $this->withContent();
//        }
    }

    /**
     * 卡片内容.
     *
     * @param int $finished
     * @param int $pending
     *
     * @return $this
     */
    public function withContent()
    {
        $today_view= 1;
        $today_sign= 1;
        $today_share= 1;
        $today_pay= 1;
        $total_view= 1;
        $total_sign= 1;
        $total_share= 1;
        $total_pay= 1;
        return $this->content(
            <<<HTML
<div style="padding: 20px;width: 100%">
<table border="1">
<tr>
  <td  style="padding: 10px; width: 400px;background-color: wheat;">今日浏览人数</td>
  <td  style="padding: 10px; width: 400px;">$today_view</td>
  <td  style="padding: 10px; width: 400px;background-color: wheat;">今日报名人数</td>
  <td  style="padding: 10px; width: 400px;">$today_sign</td>
</tr>
<tr>
  <td  style="padding: 10px; width: 400px;background-color: wheat;">今日分享人数</td>
  <td  style="padding: 10px; width: 400px;">$today_share</td>
  <td  style="padding: 10px; width: 400px;background-color: wheat;">今日付款金额</td>
  <td  style="padding: 10px; width: 400px;">$today_pay</td>
</tr>
<tr>
  <td  style="padding: 10px; width: 400px;background-color: wheat;">总浏览人数</td>
  <td  style="padding: 10px; width: 400px;">$total_view</td>
  <td  style="padding: 10px; width: 400px;background-color: wheat;">总报名人数</td>
  <td  style="padding: 10px; width: 400px;">$total_sign</td>
</tr>
<tr>
  <td  style="padding: 10px; width: 400px;background-color: wheat;">总分享人数</td>
  <td  style="padding: 10px; width: 400px;">$total_share</td>
  <td  style="padding: 10px; width: 400px;background-color: wheat;">总付款金额</td>
  <td  style="padding: 10px; width: 400px;">$total_pay</td>
</tr>
</div>
HTML
        );
    }
}
