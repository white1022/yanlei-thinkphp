<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Goods as GoodsService;
use app\common\validate\Goods as GoodsValidate;
use think\Request;
use think\response\Json;

class Goods extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = GoodsService::getGoodsList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 添加
     */
    public function add() :Json
    {
        (new GoodsValidate())->goCheck('add');
        GoodsService::addGoodsInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改
     */
    public function edit() :Json
    {
        (new GoodsValidate())->goCheck('edit');
        GoodsService::editGoodsInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new GoodsValidate())->goCheck('delete');
        GoodsService::deleteGoodsInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取商品信息
     */
    public function info() :Json
    {
        $info = GoodsService::getGoodsInfo();
        return returnResponse(200, '成功', $info);
    }

    /*
     * 获取商品分类
     */
    public function getGoodsCategory() :Json
    {
        $list = GoodsService::getGoodsCategoryList();
        return returnResponse(200, '成功', $list);
    }
}
