<?php


namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityGroup;
use App\Models\ActivitySignUser;
use App\Models\CompanyCourse;
use App\Models\UserActivityInvite;
use App\Models\WxPay;
use Illuminate\Http\Request;

class PayController extends Controller
{

    /**
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
     * 使用场景举例
     * 1、服务商抽成
     * 在各个行业中，服务商为特约商户提供增值服务，服务商与特约商户协商，可以从特约商户的交易流水中抽取一定的手续费。
     * 2、员工奖励
     * 零售、餐饮等行业中，当销售人员销售完成后，达到可奖励的条件，可以通过分账，将销售奖励分给员工。
     * 3、管理资金到账时间
     * 在酒店行业中，利用分账功能中的“冻结/解冻”能力，当用户预订/入住酒店时，交易资金先冻结在酒店的账户中，当用户确认消费离店后，再利用“分账”功能中的“分账完结”解冻资金到酒店的账户中。这样可以避免用户退款时商户账户资金不足的情况。
     * 4、分润给合作伙伴
     * 在与他方合作的情况下，可以用“分账”功能，将交易资金分给合作伙伴，例如物流合作商。
     *
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

        $validator = \Validator::make($inputs, [
            'activity_id' => 'required',
            'type' => 'required',
            'sign_name' => 'required',
            'sign_mobile' => 'required',
        ], [
            'activity_id.required' => '活动ID必填',
            'type.required' => '开团类型必填：1开团 2单独购买',
            'sign_name.required' => '报名学生姓名必填',
            'sign_mobile.required' => '报名手机号必填',
        ]);
        if ($validator->fails()) {
            return self::parametersIllegal($validator->messages()->first());
        }
        $activity_id = $inputs['activity_id'];
        $activity = Activity::getActivityById($activity_id);
        $is_many = $activity->is_many;

        if ($is_many == Activity::is_many_多商家) {
            $validator2 = \Validator::make($inputs, [
                'sign_age' => 'required',
                'sign_sex' => 'required',
                'is_agree' => 'required',
                'course_ids' => 'required',
                'school_child_ids' => 'required',
            ], [
                'sign_age.required' => '报名学生年龄必填',
                'sign_sex.required' => '性别必填：1男2女',
                'is_agree.required' => '必须同意协议',
                'course_ids.required' => '课程必填',
                'school_child_ids.required' => '校区必填',
            ]);
            if ($validator2->fails()) {
                return self::parametersIllegal($validator2->messages()->first());
            }
        }

        if ($is_many == Activity::is_many_单商家) {
            $validator3 = \Validator::make($inputs, [
                'info_one' => 'required',
                'info_two' => 'required',
            ], [
                'info_one.required' => '信息一必填',
                'info_two.required' => '信息二必填',
            ]);
            if ($validator3->fails()) {
                return self::parametersIllegal($validator3->messages()->first());
            }
        }
        $order_number = rand(1111, 9999) . date('Ymdhis') . $user_id;
        $inputs['order_num'] = $order_number;

        //总金额 最低为一分 必须是整数
        if ($inputs['type'] == ActivitySignUser::Type_直接买) {
            $fee = $activity->ori_price;
        } else {
            $fee = $activity->real_price;
        }
        $inputs['money'] = $fee;
        // 新建订单
        $order = ActivitySignUser::createOrder($inputs);
        if (!$order) {
            return self::error('10001', '创建订单失败');
        }

        $open_id = self::authUserOpenId();
        $shops = [
            'sub_key' => env('sub_key'),
            'sub_appid' => env('sub_appid'),
            'sub_mch_id' => env('sub_mch_id'),
        ];
        //参数    用户openid    订单号    支付的备注信息，商品描述   金额   是否分账    是否是商家
        $obj = new Wxpay($open_id, $order_number, '支付', $fee, 'Y', $shops);
        $pay = $obj->pay();//下单获取返回值
        $info['code'] = 1;
        $info['data'] = $pay;
        return ['code' => 1, 'data' => $info];
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
        $postXml = file_get_contents("php://input"); //接收微信参数
        if (empty($postXml)) {
            return false;
        }
        $attr = $this->xmlToArray($postXml);
        $out_trade_no = $attr['out_trade_no'];

        //-----------------------支付成功后的操作-----------------------------
        $order_no = $out_trade_no;
        $order = ActivitySignUser::query()->where('order_no', $order_no)->first();

        // 支付成功，增加课程的已售份数
        CompanyCourse::updatePayInfo($order);
        // 1. 新建团：如果type是开团，group_id 不存在，才新建团，否则去修改团里面的信息
        if ($order->type == ActivitySignUser::Type_团) {
            ActivityGroup::updatePayInfo($order);
        }
        // 2. 给邀请人分发奖励:也就是把邀请记录表支付状态变成已支付

        // 3. 修改订单的状态及信息
        ActivitySignUser::updatePayInfo($order_no);
        // 4. 修改邀请记录的支付状态
        UserActivityInvite::updatePayInfo($order);
        // 5. 生成用户的专属分享图片


//        //支付成功后在回调方法中进行分账
//        $this->profitsharing($shops['appid'],$shops['sub_mch_id'],$order['transaction_id']);
    }

    function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring), true);
        return $val;
    }
}
