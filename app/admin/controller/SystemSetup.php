<?php

namespace app\admin\controller;

use app\common\service\SystemSetup as SystemSetupService;
use app\common\validate\SystemSetup as SystemSetupValidate;
use think\response\Json;

class SystemSetup extends Base
{
    /*
     * 修改
     */
    public function edit() :Json
    {
        (new SystemSetupValidate())->goCheck('edit');
        SystemSetupService::editSystemSetupInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取系统设置信息
     */
    public function info() :Json
    {
        $info = SystemSetupService::getSystemSetupInfo();
        return returnResponse(200, '成功', $info);
    }
}