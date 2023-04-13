<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\GoodsCategory as GoodsCategoryService;
use app\common\validate\GoodsCategory as GoodsCategoryValidate;
use think\Request;
use think\response\Json;

class GoodsCategory extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = GoodsCategoryService::getGoodsCategoryList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 添加
     */
    public function add() :Json
    {
        (new GoodsCategoryValidate())->goCheck('add');
        GoodsCategoryService::addEditGoodsCategoryInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改
     */
    public function edit() :Json
    {
        (new GoodsCategoryValidate())->goCheck('edit');
        GoodsCategoryService::addEditGoodsCategoryInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new GoodsCategoryValidate())->goCheck('delete');
        GoodsCategoryService::deleteGoodsCategoryInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取父级分类
     */
    public function pid() :Json
    {
        $list = GoodsCategoryService::getPidList();
        return returnResponse(200, '成功', $list);
    }
}
