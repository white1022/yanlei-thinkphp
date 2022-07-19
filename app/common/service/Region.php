<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Region as RegionModel;

class Region
{
    /*
     * 获取地区列表
     */
    public static function getRegionList() :array
    {
        list($page, $limit) = get_page_limit();
        $name = input('get.name', '');

        $condition = [];
        if(!empty($name)){
            array_push($condition, ['name','like','%'.$name.'%']);
        }

        $list = RegionModel::where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        $total = RegionModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加/修改地区信息
     */
    public static function addEditRegionInfo() :void
    {
        if(input('post.id')){
            $info = RegionModel::findOrEmpty(input('post.id'));
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            if($info->id == input('post.pid')) throw new BadRequestException(['errorMessage' => '父级不能为自己']);
            $res = $info->save(input('post.'));
        }else{
            $res = (new RegionModel())->save(input('post.'));
        }
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 删除地区信息
     */
    public static function deleteRegionInfo() :void
    {
        $ids = explode(',', input('post.id'));
        foreach ($ids as $id){
            $info = RegionModel::where('id', '=', $id)
                ->findOrEmpty();
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $count = RegionModel::where('pid', '=', $info->id)
                ->count();
            if($count) throw new BadRequestException(['errorMessage' => '存在下级地区']);
            $res = $info->delete();
            if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
        }
    }

    /*
     * 获取父级列表
     */
    public static function getPidList() :array
    {
        $region = RegionModel::select()->toArray();
        return generateTreeMap($region);
    }
}