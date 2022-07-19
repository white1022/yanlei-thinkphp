<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Express as ExpressService;
use app\common\validate\Express as ExpressValidate;
use think\Request;
use think\response\Json;

class Express extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = ExpressService::getExpressList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 添加
     */
    public function add() :Json
    {
        (new ExpressValidate())->goCheck('add');
        ExpressService::addEditExpressInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改
     */
    public function edit() :Json
    {
        (new ExpressValidate())->goCheck('edit');
        ExpressService::addEditExpressInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new ExpressValidate())->goCheck('delete');
        ExpressService::deleteExpressInfo();
        return returnResponse(200, '成功', []);
    }

}
