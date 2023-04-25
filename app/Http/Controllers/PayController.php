<?php


namespace App\Http\Controllers;

use App\Http\Traits\PaySuccessTrait;
use App\Models\Activity;
use App\Models\ActivityFormField;
use App\Models\ActivityGroup;
use App\Models\ActivitySignUser;
use App\Models\Pay;
use App\Models\UserViewCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayController extends Controller
{

    use PaySuccessTrait;

    /**
     *服务商拓展的商户就属于特约商户，普通商户授权某些功能有了服务商也会变为特约商户
     *
     *
     * Server_appid= //服务号的appid
     * Server_mch_id= //服务商商户号
     * sub_key //特约商户的key
     * sub_appid= //子商户(特约商户)的appid
     * sub_mch_id= //子商户(特约商户)商户号
     *
     *
     *
     * https://www.freesion.com/article/34881028190/
     * 1,需要在微信服务商后台绑定特约商户 和绑定小程序 特约商户申请可以在小程序内部实现。可以自己写接口 后期咱们在写
     * 2,服务商有单独的appid和secret和小程序的appid和secret不一致
     * 3,特约商户有自己的商户号和密钥 与服务商的商户号和密钥不一致
     * 4,在统一下单的时候只有参数’profit_sharing’=>'Y’的时候才会是服务商模式支付。否则就是普通小程序支付
     * 5,服务商需要设置分账比例最低百分之30
     * 6,上述代码已上线，能够正常使用。如果在使用过程中报错，或者返回值错误请检查服务商appid、secret、特约商户商户号和密钥、小程序appid、secret等参数是否正确没有用错
     * 7,服务商分账时使用的证书为服务商证书和微信支付的证书不一致
     * 原文链接：https://blog.csdn.net/weixin_43202928/article/details/119024929
     */


    public function pay(Request $request)
    {
        $inputs = $request->all();
        $user_id = self::authUserId();
        $inputs['uid'] = $user_id;
        $inputs['user_id'] = $user_id;

        $validator = \Validator::make($inputs, [
            'activity_id' => 'required',
            'type' => 'required',
        ], [
            'activity_id.required' => '活动ID必填',
            'type.required' => '开团类型必填：1开团 2单独购买',
        ]);
        if ($validator->fails()) {
            return self::parametersIllegal($validator->messages()->first());
        }

        if (isset($inputs['group_id']) && $inputs['group_id']) {
            $group = ActivityGroup::query()->find($inputs['group_id']);
            if ($group && $group->finished == 1) {
                return self::error('10008', '已满团');
            }
        }

        $activity_id = $inputs['activity_id'];
        $activity_form_fields_3 = ActivityFormField::query()->where('activity_id', $activity_id)
            ->where('type', 3)->get();
        $activity_form_fields_3_cols = [];
        $num_key = [];
        foreach ($activity_form_fields_3 as $field) {
            if ($field->select_num > 0) {
                $num_key[$field->field_name] = $field->select_num;
                $activity_form_fields_3_cols[] = $field->field_name;
            }
        }

        if (isset($inputs['info']) && $inputs['info']) {
            $info_array = json_decode($inputs['info'], true);
            foreach ($info_array as $key => $value) {
                if (in_array($key, $activity_form_fields_3_cols)) {
                    if ($num_key[$key] != count($value)) {
                        return self::error('10007', $key . '请选择' . $num_key[$key] . '项');
                    }
                }
            }
        }
        $activity = Activity::getActivityById($activity_id);
        if ($inputs['type'] == ActivitySignUser::Type_直接买) {
            $fee = $activity->ori_price;
        } else {
            $fee = $activity->real_price;
        }
        $inputs['money'] = $fee;//根据购买方式，获取支付的金额 最低为一分 必须是整数
        $order_number = 'Or' . rand(1111, 9999) . '-' . date('Ymdhis') . '-' . $user_id;
        $inputs['order_num'] = $order_number;
        $order_id = ActivitySignUser::createOrder($inputs);// 新建订单
        if (!$order_id) {
            return self::error('10001', '创建订单失败');
        }

        UserViewCount::query()->where('activity_id', $activity_id)
            ->where('user_id', $user_id)
            ->orderBy('id', 'desc')->update([
                'has_info' => 1
            ]);//预留了信息

//        正式支付开始
//        $obj = new Pay();
//        $info = $obj->paytwo($order_number);
//        return self::success($info);

        //        测试支付用
        $order_obj = ActivitySignUser::query()->find($order_id);
        return self::success($this->paySuccessDeal($order_obj));
    }


    /**
     * 支付成功
     * 增加课程的已售份数
     * 新建团或修改团信息：如果type是开团，group_id 不存在，才新建团，否则去修改团里面的信息
     * 给邀请人分发奖励:也就是把邀请记录表支付状态变成已支付
     * 修改订单的状态及信息
     * 修改邀请记录的支付状态
     * 生成用户的专属分享图片
     *
     * @return bool
     */
    public function notify()
    {
//        Log::info('支付成功后的回调:', ['pay_notify' => 'success']);
        $postXml = file_get_contents("php://input"); //接收微信参数
        if (empty($postXml)) {
            return false;
        }
        $attr = $this->xmlToArray($postXml);
        $out_trade_no = $attr['out_trade_no'];
//        Log::info('out_trade_no:', ['out_trade_no' => $out_trade_no]);
//        Log::info('out_trade_no:', ['attr' => $attr]);
        //-----------------------支付成功后的操作-----------------------------
        $order_no = $out_trade_no;
        $order = ActivitySignUser::query()->where('order_no', $order_no)->first();
        if ($order && $order->has_pay == 2) {
            Log::info('pay_notify:', ['info:' => '支付成功，更新订单']);
            return $this->paySuccessDeal($order);
        } else {
            Log::info('pay_notify:', ['info:' => '重复的支付成功后的回调']);
        }
    }

    function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring), true);
        return $val;
    }


    /**
     * 微信退款回调
     */
    public function refundsnotify(WxPayV3 $wxPayV3){
        return $wxPayV3->refundsNotify(function($res){
            //解密消息$res，可以参考下面
            //处理订单业务逻辑
        });
    }
}
