<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Address as AddressService;
use app\common\validate\Address as AddressValidate;
use think\Request;
use think\response\Json;

class Address extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = AddressService::getAddressList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new AddressValidate())->goCheck('delete');
        AddressService::deleteAddressInfo();
        return returnResponse(200, '成功', []);
    }
}
