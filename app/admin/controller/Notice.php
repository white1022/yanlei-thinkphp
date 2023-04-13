<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Notice as NoticeService;
use app\common\validate\Notice as NoticeValidate;
use think\Request;
use think\response\Json;

class Notice extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = NoticeService::getNoticeList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 添加
     */
    public function add() :Json
    {
        (new NoticeValidate())->goCheck('add');
        NoticeService::addEditNoticeInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改
     */
    public function edit() :Json
    {
        (new NoticeValidate())->goCheck('edit');
        NoticeService::addEditNoticeInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new NoticeValidate())->goCheck('delete');
        NoticeService::deleteNoticeInfo();
        return returnResponse(200, '成功', []);
    }

}
