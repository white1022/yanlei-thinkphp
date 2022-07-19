<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Storey as StoreyModel;
use app\common\model\GoodsCategory as GoodsCategoryModel;

class Storey
{
    /*
     * 获取楼层列表
     */
    public static function getStoreyList() :array
    {
        list($page, $limit) = get_page_limit();

        $condition = [];

        $list = StoreyModel::with(['goodsCategory'])->where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        $total = StoreyModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加/修改楼层信息
     */
    public static function addEditStoreyInfo() :void
    {
        if(input('post.id')){
            $info = StoreyModel::findOrEmpty(input('post.id'));
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $res = $info->save(input('post.'));
        }else{
            $res = (new StoreyModel())->save(input('post.'));
        }
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 删除楼层信息
     */
    public static function deleteStoreyInfo() :void
    {
        $res = StoreyModel::where([
            ['id', 'in', explode(',', input('post.id'))],
        ])->delete();
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 获取商品分类列表
     */
    public static function getGoodsCategoryList() :array
    {
        return GoodsCategoryModel::where('pid', '=', 0)
            ->order(['sort'=>'asc','id'=>'desc'])
            ->select()
            ->toArray();
    }
}