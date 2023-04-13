<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Log as LogService;
use app\common\validate\Log as LogValidate;
use think\response\Json;

class Log
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = LogService::getLogList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new LogValidate())->goCheck('delete');
        LogService::deleteLogInfo();
        return returnResponse(200, '成功', []);
    }
}
