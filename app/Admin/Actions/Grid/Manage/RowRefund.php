<?php

namespace App\Admin\Actions\Grid\Manage;

use App\Models\ActivityGroup;
use App\Models\ActivitySignUser;
use App\Models\User;
use App\Models\WXRefund;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RowRefund extends RowAction
{
    /**
     * @return string
     */
    protected $title = '<span class="btn btn-sm btn-info" style="margin-left: 5px; margin-top: 2px;">退款</span>';

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        $id = $this->getKey();
        $order = ActivitySignUser::getActivitySignUserById($id);
        $user = \App\User::getUserById($order->user_id);
        $refund_order_no = $order->refund_order_no;
        if (!$refund_order_no) {
            $refund_order_no = 'Ror_' . time() . '_' . $order->user_id;
            ActivitySignUser::query()->where('id', $id)->update(['refund_order_no' => $refund_order_no]);
        }
        $refund = new WXRefund($user->openid, $order->order_no, $order->money, $refund_order_no, $order->money);
        $res = $refund->refund();
//        if(退款成功){
//            1.更新订单表
//            2.更新邀请表
//            3.更新组团数据
//        }
        return $this->response()
            ->success('退款: ' . $res);
    }

    /**
     * @return string|array|void
     */
    public function confirm()
    {
        return ['确定?', '您确定要退款么？'];
    }

    /**
     * @param Model|Authenticatable|HasPermissions|null $user
     *
     * @return bool
     */
    protected function authorize($user): bool
    {
        return true;
    }

    /**
     * @return array
     */
    protected function parameters()
    {
        return [];
    }
}
