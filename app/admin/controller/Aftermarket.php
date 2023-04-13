<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Aftermarket as AftermarketService;
use app\common\validate\Aftermarket as AftermarketValidate;
use think\response\Json;

class Aftermarket extends Base
{
    /*
     * 修改
     */
    public function edit() :Json
    {
        (new AftermarketValidate())->goCheck('edit');
        AftermarketService::editAftermarketInfo();
        return returnResponse(200, '成功', []);
    }

}
