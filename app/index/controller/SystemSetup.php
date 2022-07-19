<?php
declare (strict_types = 1);

namespace app\index\controller;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\SystemSetup as SystemSetupModel;
use think\response\Json;

class SystemSetup extends Base
{
    /*
     * 获取系统设置信息
     */
    public function getInformation() :Json
    {
        $info = SystemSetupModel::findOrEmpty(1);
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在']);
        return returnResponse(200, '成功', $info);
    }
}