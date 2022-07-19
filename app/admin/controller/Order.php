<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Order as OrderService;
use app\common\validate\Order as OrderValidate;
use think\response\Json;

class Order extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = OrderService::getOrderList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new OrderValidate())->goCheck('delete');
        OrderService::deleteOrderInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取订单信息
     */
    public function info() :Json
    {
        $info = OrderService::getOrderInfo();
        return returnResponse(200, '成功', $info);
    }

    /*
     * 订单发货
     */
    public function delivery() :Json
    {
        (new OrderValidate())->goCheck('delivery');
        $info = OrderService::orderDelivery();
        return returnResponse(200, '成功', $info);
    }

    /*
     * 获取快递
     */
    public function getExpress() :Json
    {
        $info = OrderService::getExpressList();
        return returnResponse(200, '成功', $info);
    }

    /*
     * 打印预览
     */
    public function printPreview() :Json
    {
        $data = OrderService::getPrintPreview($this->adminId);
        return returnResponse(200, '成功', $data);
    }
}
