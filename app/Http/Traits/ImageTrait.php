<?php
namespace App\Http\Traits;

use App\Http\Constants\ResponseCodeConstant;
use Illuminate\Http\JsonResponse;
use stdClass;

/**
 * Created by PhpStorm.
 * User: codeanti
 * Date: 2020-1-4
 * Time: 下午3:08
 */
trait ImageTrait
{
    /**
     * 获取图片尺寸信息
     * @param $imagePath
     * @return array|bool
     */
    public static function imageSize($imagePath)
    {
        if ($imagePath) {
            return getimagesize($imagePath);
        }
        return [];
    }
}
