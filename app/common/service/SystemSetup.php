<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\SystemSetup as SystemSetupModel;

class SystemSetup
{
    /*
     * 修改系统设置信息
     */
    public static function editSystemSetupInfo() :void
    {
        $info = SystemSetupModel::findOrEmpty(1);
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        $res = $info->save(input('post.'));
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 获取系统设置信息
     */
    public static function getSystemSetupInfo() :array
    {
        $info = SystemSetupModel::findOrEmpty(1);
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        return $info->toArray();
    }
}