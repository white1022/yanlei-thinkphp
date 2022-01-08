<?php

namespace app\admin\controller;

use app\common\service\Region as RegionService;
use app\common\validate\Region as RegionValidate;
use think\response\Json;

class Region extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = RegionService::getRegionList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 添加
     */
    public function add() :Json
    {
        (new RegionValidate())->goCheck('add');
        RegionService::addEditRegionInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改
     */
    public function edit() :Json
    {
        (new RegionValidate())->goCheck('edit');
        RegionService::addEditRegionInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new RegionValidate())->goCheck('delete');
        RegionService::deleteRegionInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取父级地区
     */
    public function pid() :Json
    {
        $list = RegionService::getPidList();
        return returnResponse(200, '成功', $list);
    }
}