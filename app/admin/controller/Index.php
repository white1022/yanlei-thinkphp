<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Index as IndexService;
use think\response\Json;

class Index extends Base
{
    /*
     * 获取控制台数据
     */
    public function console() :Json
    {
        $backlog = IndexService::getBacklog();
        $statistics = IndexService::getStatistics();
        $orderChart = IndexService::getOrderChart();
        $userChart = IndexService::getUserChart();
        return returnResponse(200, '成功', [
            'backlog' => $backlog,
            'statistics' => $statistics,
            'chart' => [
                'order' => $orderChart,
                'user' => $userChart,
            ],
        ]);
    }
}
