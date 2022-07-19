<?php

namespace app\index\controller;

use app\common\service\Storage as StorageService;
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
}