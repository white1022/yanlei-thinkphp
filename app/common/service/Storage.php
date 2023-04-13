<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use think\facade\Filesystem;
use think\facade\Request;
use think\response\File;

/*
 * 存储
 */
class Storage
{
    /*
     * 上传
     */
    public static function upload(string $name = 'file') :string
    {
        // 获取上传文件
        $file = Request::file($name);
        if(!$file) throw new BadRequestException(['errorMessage' => '文件不存在']);
        // 移动到服务器的上传目录
        $saveName = Filesystem::disk('public')->putFile('/', $file);
        // 拼接url路径
        $url = Filesystem::getDiskConfig('public', 'url') . '/' . $saveName;
        // 替换字符串
        return str_replace('\\', '/', $url);
    }

    /*
     * 下载
     */
    public static function download(string $file_name = '', string $name = '') :File
    {
        // todo 不知道有没有自动加上前缀 Request::domain()
        return download($file_name, $name);
    }
}
