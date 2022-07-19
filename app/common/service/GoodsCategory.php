<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\GoodsCategory as GoodsCategoryModel;
use app\common\model\Goods as GoodsModel;
use app\common\model\Storey as StoreyModel;

class GoodsCategory
{
    /*
     * 获取商品分类列表
     */
    public static function getGoodsCategoryList() :array
    {
        list($page, $limit) = get_page_limit();
        $pid = input('get.pid', '');
        $name = input('get.name', '');

        $condition = [];
        if(!empty($pid)){
            array_push($condition, ['pid','=',$pid]);
        }
        if(!empty($name)){
            array_push($condition, ['name','like','%'.$name.'%']);
        }

        $list = GoodsCategoryModel::where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        $total = GoodsCategoryModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加/修改商品分类信息
     */
    public static function addEditGoodsCategoryInfo() :void
    {
        if(input('post.id')){
            $info = GoodsCategoryModel::findOrEmpty(input('post.id'));
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            if($info->id == input('post.pid')) throw new BadRequestException(['errorMessage' => '父级不能为自己']);
            $res = $info->save(input('post.'));
        }else{
            $res = (new GoodsCategoryModel())->save(input('post.'));
        }
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 删除商品分类信息
     */
    public static function deleteGoodsCategoryInfo() :void
    {
        $ids = explode(',', input('post.id'));
        foreach ($ids as $id){
            $info = GoodsCategoryModel::where('id', '=', $id)
                ->findOrEmpty();
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $count = GoodsCategoryModel::where('pid', '=', $info->id)
                ->count();
            if($count) throw new BadRequestException(['errorMessage' => '存在下级商品分类']);
            $count = GoodsModel::where('first_goods_category_id', '=', $info->id)
                ->whereOr('second_goods_category_id', '=', $info->id)
                ->whereOr('third_goods_category_id', '=', $info->id)
                ->count();
            if($count) throw new BadRequestException(['errorMessage' => '存在商品正在使用该分类']);
            $count = StoreyModel::where('goods_category_id', '=', $info->id)
                ->count();
            if($count) throw new BadRequestException(['errorMessage' => '存在楼层正在使用该分类']);
            $res = $info->delete();
            if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
        }
    }

    /*
     * 获取父级列表
     */
    public static function getPidList() :array
    {
        $goodsCategory = GoodsCategoryModel::order(['sort'=>'asc','id'=>'desc'])->select()->toArray();
        return generateTreeMap($goodsCategory);
    }
}