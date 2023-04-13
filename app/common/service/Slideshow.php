<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Slideshow as SlideshowModel;

class Slideshow
{
    /*
     * 获取轮播图列表
     */
    public static function getSlideshowList() :array
    {
        list($page, $limit) = get_page_limit();
        $name = input('get.name', '');

        $condition = [];
        if(!empty($name)){
            array_push($condition, ['name','like','%'.$name.'%']);
        }

        $list = SlideshowModel::where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        $total = SlideshowModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加/修改轮播图信息
     */
    public static function addEditSlideshowInfo() :void
    {
        if(input('post.id')){
            $info = SlideshowModel::findOrEmpty(input('post.id'));
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $res = $info->save(input('post.'));
        }else{
            $res = (new SlideshowModel())->save(input('post.'));
        }
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 删除轮播图信息
     */
    public static function deleteSlideshowInfo() :void
    {
        $res = SlideshowModel::where([
            ['id', 'in', explode(',', input('post.id'))],
        ])->delete();
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }
}