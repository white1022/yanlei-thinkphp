<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Feedback as FeedbackModel;

class Feedback
{
    /*
     * 获取意见反馈列表
     */
    public static function getFeedbackList() :array
    {
        list($page, $limit) = get_page_limit();
        $status = input('get.status', '');

        $condition = [];
        if(!empty($status)){
            array_push($condition, ['status','=',$status]);
        }

        $list = FeedbackModel::where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        foreach ($list as $item){
            $item->user; //获取意见反馈的用户
        }
        $total = FeedbackModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加/修改意见反馈信息
     */
    public static function addEditFeedbackInfo() :void
    {
        if(input('post.id')){
            $info = FeedbackModel::findOrEmpty(input('post.id'));
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $res = $info->save(input('post.'));
        }else{
            $res = (new FeedbackModel())->save(input('post.'));
        }
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 删除意见反馈信息
     */
    public static function deleteFeedbackInfo() :void
    {
        $res = FeedbackModel::where([
            ['id', 'in', explode(',', input('post.id'))],
        ])->delete();
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }
}