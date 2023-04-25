<?php

namespace App\Models;


use App\User;
use Illuminate\Database\Eloquent\Model;

use EasyWeChat\Factory;

class UserApplyCashOut extends Model
{

    protected $table = 'user_apply_cash_out';

    const Status_审核状态_待审核 = 1;

    const Status_审核状态_拒绝 = 3;

    const Status_审核状态 = [
        1 => '待审核',
        2 => '通过',
        3 => '拒绝',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public static function PayToUser($id)
    {
        $apply = UserApplyCashOut::query()->find($id);
        $user = \App\Models\User::getUserById($apply->user_id);
        $order_num = 'TX' . time() . 'ID' . $apply->user_id;
        UserApplyCashOut::query()->where('id', $id)->update(['order_num' => $order_num]);
        //企业打款到个人微信钱包
        $config = [
            'app_id' => env('sub_appid'),
            'mch_id' => env('sub_mch_id'),
            'key' => env('sub_key'),   // API 密钥
            'cert_path' => public_path() . '\cert\apiclient_cert.pem', // XXX: 绝对路径！！！！
            'key_path' => public_path() . '\cert\apiclient_key.pem',      // XXX: 绝对路径！！！！
            'notify_url' => 'https://zsq.a-poor.com/api/pay/txnotify',//回调地址
        ];
        $app = Factory::payment($config);
        $result = $app->transfer->toBalance([
            'partner_trade_no' => $order_num, // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
            'openid' => $user->openid,
            'check_name' => 'NO_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
            'amount' => ($apply->apply_money) * 100, // 企业付款金额，单位为分最少1元
            'desc' => '提现', // 企业付款操作说明信息。必填
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],//服务器IP
        ]);
        return json_encode($result);
    }

}
