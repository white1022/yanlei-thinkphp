<?php

namespace app\index\controller;

use app\common\model\Region as RegionModel;
use app\common\service\Region as RegionService;
use think\response\Json;

class Region extends Base
{
    /*
     * 获取地区列表
     */
    public function getList() :Json
    {
        $pid = input('get.pid', 0);
        $list = RegionModel::where('pid', '=', $pid)->select();
        return returnResponse(200, '成功', $list);
    }
}