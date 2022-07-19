<?php

namespace app\index\controller;

use app\common\model\Notice as NoticeModel;
use app\common\service\Notice as NoticeService;
use think\response\Json;

class Notice extends Base
{
    /*
     * 获取公告列表
     */
    public function getList() :Json
    {
        list($page, $limit) = get_page_limit();

        $list = NoticeModel::order(['create_time' => 'desc'])
            ->limit($limit)
            ->page($page)
            ->select();
        $total = NoticeModel::count();

        return returnResponse(200, '成功', [
            'list' => $list,
            'total' => $total,
        ]);
    }
}