<?php

namespace app\admin\controller;

use app\common\service\Storage as StorageService;
use think\response\File;
use think\response\Json;

class Storage extends Base
{
    /*
     * 上传
     */
    public function upload() :Json
    {
        $url = StorageService::upload();
        return returnResponse(200, '成功', ['url' => $url]);
    }

    /*
     * 下载
     */
    public function download() :File
    {
        return StorageService::download();
    }
}