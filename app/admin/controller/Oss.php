<?php

namespace app\admin\controller;

use app\common\service\Oss as OssService;
use think\facade\Request;
use think\response\File;
use think\response\Json;

class Oss extends Base
{
    /*
     * 上传
     */
    public function upload() :Json
    {
        $url = (new OssService())->upload();
        return returnResponse(200, '成功', ['url' => $url]);
    }

    /*
     * 下载
     */
    public function download() :File
    {
        return (new OssService())->download();
    }

    /*
     * 通过LayEdit上传
     * 插件对返回的数据格式有要求
     */
    public function uploadByLayEdit() :Json
    {
        $url = (new OssService())->upload();
        $data = [
            'code' => 0, //0表示成功，其它失败
            'msg' => '', //提示信息，一般上传失败后返回
            'data' => [
                'src' => config('config.aliyunoss.bucketBindDomain') . $url, //图片路径
                'title' => '', //图片名称，可选
            ],
        ];
        return json($data, 200);
    }
}