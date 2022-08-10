<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Http\StreamResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait WeChatTrait
{
    public function shareCodeByEasyWeChat($id, $user_id = null)
    {
        if ($user_id) {
            $scene = 'id=' . $id . '&uid=' . $user_id;
        } else {
            $scene = 'id=' . $id;
        }
        $config = [
            'app_id' => env('AppID'),
            'secret' => env('AppSecret'),
            // response_type为可选项指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',
        ];
        $path = public_path('uploads/Qrcode/');
        if (!file_exists($path)) {
            mkdir($path, 0700, true);
        }
        $filename = 'miniProgram' . time() . '.png';
        $app = Factory::miniProgram($config); // 小程序
        $response = $app->app_code->getUnlimit($scene, ["page" => 'pages/index/index']);
        Log::info('小程序$response::' . json_encode($response, JSON_UNESCAPED_UNICODE));
        if ($response instanceof StreamResponse) {
            $file = $response->saveAs($path, $filename);  //保存文件的操作
            return [
                'code' => 200,
                'msg' => 'success',
                'url' => $path . $file
            ];
        } else {
            return [
                'code' => 10001,
                'msg' => 'failed'
            ];
        }
    }

    public function getAccessToken()
    {
        $access_obj = DB::table('access_token')
            ->where('valid_time', '>', date('Y-m-d H:i:s', time()))
            ->first();
        if ($access_obj) {
            $access_token = $access_obj->access_token;
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . env('AppID') . '&secret=' . env('AppSecret');
            $token = file_get_contents($url);
            $token_array = json_decode($token, true);
            if (isset($token_array['access_token']) && $token_array['access_token']) {
                $valid_time = date('Y-m-d H:i:s', time() + $token_array['expires_in'] - 20);
                DB::table('access_token')->truncate();
                DB::table('access_token')
                    ->insert([
                        'access_token' => $token_array['access_token'],
                        'expires_in' => $token_array['expires_in'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'valid_time' => $valid_time,
                    ]);
            }
            $access_token = $token_array['access_token'];
        }
        return $access_token;
    }

    public function getShareQCode($activity_id, $user_id = null)
    {
        $qr_path = "uploads/code/";
        if (!file_exists($qr_path)) {
            mkdir($qr_path, 0777, true);
        }
        $filename = time() . '.png';
        $file = $qr_path . $filename;
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $this->getAccessToken();
        if ($user_id) {
            $scene = 'id=' . $activity_id . '&uid=' . $user_id;
        } else {
            $scene = 'id=' . $activity_id;
        }
        $param = json_encode(["scene" => $scene,"page"=>'pages/index/index',"width" => 300]);

        $contents = $this->httpRequest($url, $param, "POST");
        if(isset(json_decode($contents,true)['errcode'])){
            return [
                'code' => json_decode($contents,true)['errcode'],
                'msg' => json_decode($contents,true)['errmsg']
            ];
        }
//        $data_uri = $this->data_uri($contents, 'image/png');
//        return '<image src=' . $data_uri . '></image>';
        $res = file_put_contents($file, $contents);//将微信返回的图片数据流写入文件
        if ($res === false) {
            return [
                'code' => 10002,
                'msg' => '文件写入失败'
            ];
        } else {
            return [
                'code' => 200,
                'msg' => 'success',
                'url' => $file
            ];
        }
    }
    
    //二进制转图片image/png
    public function data_uri($contents, $mime)
    {
        $base64 = base64_encode($contents);
        return ('data:' . $mime . ';base64,' . $base64);
    }

    //把请求发送到微信服务器换取二维码
    public function httpRequest($url, $data = '', $method = 'GET')
    {
        $curl = curl_init();// 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url);// 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);// 对认证证书来源的检测
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);// 从证书中检查SSL加密算法是否存在
//        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data != '') {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }


    //开启curl post请求
    function curlPost($url, $data)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检测
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:')); //解决数据包大不能提交
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);
        }
        curl_close($curl); // 关键CURL会话
        return $tmpInfo; // 返回数据
    }

}
