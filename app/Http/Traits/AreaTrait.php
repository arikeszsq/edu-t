<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Http\StreamResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait AreaTrait
{
    public function getGeo($address)
    {
        $url = 'http://restapi.amap.com/v3/geocode/geo?address=' . $address . '&output=JSON&key=' . env('Area_key');
        $res = json_decode(file_get_contents($url), true);
        if (isset($res['infocode']) && $res['infocode'] == 10000) {
            return $res['geocodes']['location'];
        }
        return '';
    }

    public function getGap($address1, $address2)
    {
        $url = 'http://restapi.amap.com/v3/distance?origins=' . $address1 . '&destination=' . $address2 . '&output=json&key=' . env('Area_key') . '&type=1';
        $geo1 = $this->getGeo($address1);
        $geo2 = $this->getGeo($address2);
        if ($geo1 && $geo2) {
            $res = json_decode(file_get_contents($url), true);
            if (isset($res['infocode']) && $res['infocode'] == 10000) {
                return ($res['results']['distance']) / 1000;
            }
        }
        return 0;
    }
}
