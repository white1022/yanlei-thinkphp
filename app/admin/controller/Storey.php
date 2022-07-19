<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Storey as StoreyService;
use app\common\validate\Storey as StoreyValidate;
use think\Request;
use think\response\Json;

class Storey extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = StoreyService::getStoreyList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 添加
     */
    public function add() :Json
    {
        (new StoreyValidate())->goCheck('add');
        StoreyService::addEditStoreyInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改
     */
    public function edit() :Json
    {
        (new StoreyValidate())->goCheck('edit');
        StoreyService::addEditStoreyInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new StoreyValidate())->goCheck('delete');
        StoreyService::deleteStoreyInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取商品分类
     */
    public function goodsCategory() :Json
    {
        $list = StoreyService::getGoodsCategoryList();
        return returnResponse(200, '成功', $list);
    }
}
