<?php


namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivitySignUser;
use Illuminate\Http\Request;

class PayController extends Controller
{

    public function pay(Request $request)
    {
        $inputs = $request->all();
        $user_id = self::authUserId();
        $inputs['uid'] = $user_id;

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
        $activity_id = $inputs['activity_id'];
        $activity = Activity::getActivityById($activity_id);
        $is_many = $activity->is_many;

        if ($is_many == Activity::is_many_多商家) {
            $validator2 = \Validator::make($inputs, [
                'sign_name' => 'required',
                'sign_mobile' => 'required',
                'sign_age' => 'required',
                'sign_sex' => 'required',
                'is_agree' => 'required',
            ], [
                'sign_name.required' => '报名学生姓名必填',
                'sign_mobile.required' => '报名手机号必填',
                'sign_age.required' => '报名学生年龄必填',
                'sign_sex.required' => '性别必填：1男2女',
                'is_agree.required' => '必须同意协议',
            ]);
            if ($validator2->fails()) {
                return self::parametersIllegal($validator2->messages()->first());
            }
        }
        ActivitySignUser::updateSignUserInfo($inputs);
        if ($inputs['type'] == ActivitySignUser::Type_直接买) {
            $fee = $activity->ori_price;
        } else {
            $fee = $activity->real_price;
        }

        $order_number = rand(1111, 9999) . date('Ymd') . $user_id;


        $openid = '填写openid';
        $appid = env('AppID');//这里写小程序AddId
        $body = $activity->title; //这个自己写,微信订单里面显示的是商品名称
        $mch_id = env('Mch_id');//'你的商户号';
        $nonce_str = $this->nonce_str();//随机字符串
        $notify_url = env('APP_URL') . 'api/pay/notify';//'填写你的回调地址';
        $out_trade_no = $this->order_number($openid);//商户订单号
        $spbill_create_ip = '服务器Ip地址';
        $total_fee = $fee * 100;//因为充值金额最小是1 而且单位为分 如果是充值1元所以这里需要*100
        $trade_type = 'JSAPI';//交易类型 默认
        //这里是按照顺序的 因为下面的签名是按照顺序 排序错误 肯定出错
        $post['appid'] = $appid;
        $post['body'] = $body;
        $post['mch_id'] = $mch_id;
        $post['nonce_str'] = $nonce_str; //随机字符串
        $post['notify_url'] = $notify_url;
        $post['openid'] = $openid;
        $post['out_trade_no'] = $out_trade_no;
        $post['spbill_create_ip'] = $spbill_create_ip; //终端的ip
        $post['total_fee'] = $total_fee; //总金额 最低为一块钱 必须是整数
        $post['trade_type'] = $trade_type;
        $sign = $this->sign($post); //签名
        $post_xml = '<xml>
	           <appid>' . $appid . '</appid>
	           <body>' . $body . '</body>
	           <mch_id>' . $mch_id . '</mch_id>
	           <nonce_str>' . $nonce_str . '</nonce_str>
	           <notify_url>' . $notify_url . '</notify_url>
	           <openid>' . $openid . '</openid>
	           <out_trade_no>' . $out_trade_no . '</out_trade_no>
	           <spbill_create_ip>' . $spbill_create_ip . '</spbill_create_ip>
	           <total_fee>' . $total_fee . '</total_fee>
	           <trade_type>' . $trade_type . '</trade_type>
	           <sign>' . $sign . '</sign>
	        </xml> ';
        //统一接口prepay_id
        $url = 'https://api.mch.weixin.qq***/pay/unifiedorder';
        $xml = $this->http_request($url, $post_xml);
        $array = $this->xml($xml);//全要大写
        if ($array['RETURN_CODE'] == 'SUCCESS' && $array['RESULT_CODE'] == 'SUCCESS') {
            $time = time();
            $tmp = '';//临时数组用于签名
            $tmp['appId'] = $appid;
            $tmp['nonceStr'] = $nonce_str;
            $tmp['package'] = 'prepay_id=' . $array['PREPAY_ID'];
            $tmp['signType'] = 'MD5';
            $tmp['timeStamp'] = "$time";
            $data['state'] = 1;
            $data['timeStamp'] = "$time";//时间戳
            $data['nonceStr'] = $nonce_str;//随机字符串
            $data['signType'] = 'MD5';//签名算法，暂支持 MD5
            $data['package'] = 'prepay_id=' . $array['PREPAY_ID'];//统一接口返回的 prepay_id 参数值，提交格式如：prepay_id=*
            $data['paySign'] = $this->sign($tmp);//签名,具体签名方案参见微信公众号支付帮助文档;
            $data['out_trade_no'] = $out_trade_no;

        } else {
            $data['state'] = 0;
            $data['text'] = "错误";
            $data['RETURN_CODE'] = $array['RETURN_CODE'];
            $data['RETURN_MSG'] = $array['RETURN_MSG'];
        }
        echo json_encode($data);
    }

    public function notify($type)
    {

    }


    //随机32位字符串
    private function nonce_str()
    {
        $result = '';
        $str = 'QWERTYUIOPASDFGHJKLZXVBNMqwertyuioplkjhgfdsamnbvcxz';
        for ($i = 0; $i < 32; $i++) {
            $result .= $str[rand(0, 48)];
        }
        return $result;
    }

    //生成订单号
    private function order_number($openid)
    {
        //date('Ymd',time()).time().rand(10,99);//18位
        return md5($openid . time() . rand(10, 99));//32位
    }

    //签名 $data要先排好顺序
    private function sign($data)
    {
        $stringA = '';
        foreach ($data as $key => $value) {
            if (!$value) continue;
            if ($stringA) $stringA .= '&' . $key . "=" . $value;
            else $stringA = $key . "=" . $value;
        }
        $wx_key = env('Wx_pay_key');//'支付秘钥'
        $stringSignTemp = $stringA . '&key=' . $wx_key;//申请支付后有给予一个商户账号和密码，登陆后自己设置key
        return strtoupper(md5($stringSignTemp));
    }

    //curl请求啊
    function http_request($url, $data = null, $headers = array())
    {
        $curl = curl_init();
        if (count($headers) >= 1) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    //获取xml
    private function xml($xml)
    {
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $vals, $index);
        xml_parser_free($p);
        $data = "";
        foreach ($index as $key => $value) {
            if ($key == 'xml' || $key == 'XML') continue;
            $tag = $vals[$value[0]]['tag'];
            $value = $vals[$value[0]]['value'];
            $data[$tag] = $value;
        }
        return $data;
    }
}
