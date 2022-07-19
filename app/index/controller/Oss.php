<?php

namespace app\index\controller;

use app\common\service\Oss as OssService;
use think\response\Json;

class Oss extends Base
{
    /*
     * 上传
     */
    public function upload() :Json
    {
        $oss = new OssService();
        $url = $oss->upload();
        return returnResponse(200, '成功', ['url' => $url]);
    }

}