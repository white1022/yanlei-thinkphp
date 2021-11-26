<?php
declare (strict_types = 1);

namespace app\common\model;

use think\facade\Request;
use think\Model;

/**
 * @mixin \think\Model
 */
class Base extends Model
{
    /*
     * 添加域名前缀
     */
    protected function prefixUrl($value, $data)
    {
        $finalUrl = Request::domain() . $value;
        return $finalUrl;
    }

    /*
     * 获取IP地址
     */
    protected function ipAddress($value, $data)
    {
        return Request::ip();
    }

    /*
     * 哈希加密
     */
    protected function hashEncryption($value, $data)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }
}
