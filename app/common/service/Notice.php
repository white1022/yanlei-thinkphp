<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Notice as NoticeModel;

class Notice
{
    /*
     * 获取公告列表
     */
    public static function getNoticeList() :array
    {
        list($page, $limit) = get_page_limit();
        $title = input('get.title', '');

        $condition = [];
        if(!empty($title)){
            array_push($condition, ['title','like','%'.$title.'%']);
        }

        $list = NoticeModel::where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        $total = NoticeModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加/修改公告信息
     */
    public static function addEditNoticeInfo() :void
    {
        if(input('post.id')){
            $info = NoticeModel::findOrEmpty(input('post.id'));
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $res = $info->save(input('post.'));
        }else{
            $res = (new NoticeModel())->save(input('post.'));
        }
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 删除公告信息
     */
    public static function deleteNoticeInfo() :void
    {
        $res = NoticeModel::where([
            ['id', 'in', explode(',', input('post.id'))],
        ])->delete();
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }
}