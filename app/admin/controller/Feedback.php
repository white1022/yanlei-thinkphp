<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Feedback as FeedbackService;
use app\common\validate\Feedback as FeedbackValidate;
use think\Request;
use think\response\Json;

class Feedback extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = FeedbackService::getFeedbackList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new FeedbackValidate())->goCheck('delete');
        FeedbackService::deleteFeedbackInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 处理
     */
    public function process() :Json
    {
        (new FeedbackValidate())->goCheck('process');
        FeedbackService::addEditFeedbackInfo();
        return returnResponse(200, '成功', []);
    }
}
