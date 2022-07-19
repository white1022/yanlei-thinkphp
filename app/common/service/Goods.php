<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Goods as GoodsModel;
use app\common\model\GoodsCategory as GoodsCategoryModel;
use app\common\model\GoodsSpecification as GoodsSpecificationModel;
use app\common\model\GoodsParameter as GoodsParameterModel;
use app\common\model\OrderGoods as OrderGoodsModel;

class Goods
{
    /*
     * 获取商品列表
     */
    public static function getGoodsList() :array
    {
        list($page, $limit) = get_page_limit();
        $first_goods_category_id = input('get.first_goods_category_id', '');
        $second_goods_category_id = input('get.second_goods_category_id', '');
        $third_goods_category_id = input('get.third_goods_category_id', '');
        $is_recommend_index = input('get.is_recommend_index', '');
        $is_recommend_hot = input('get.is_recommend_hot', '');
        $is_recommend_blast = input('get.is_recommend_blast', '');
        $status = input('get.status', '');
        $name = input('get.name', '');

        $condition = [];
        if(!empty($first_goods_category_id)){
            array_push($condition, ['first_goods_category_id','=',$first_goods_category_id]);
        }
        if(!empty($second_goods_category_id)){
            array_push($condition, ['second_goods_category_id','=',$second_goods_category_id]);
        }
        if(!empty($third_goods_category_id)){
            array_push($condition, ['third_goods_category_id','=',$third_goods_category_id]);
        }
        if(!empty($is_recommend_index)){
            array_push($condition, ['is_recommend_index','=',$is_recommend_index]);
        }
        if(!empty($is_recommend_hot)){
            array_push($condition, ['is_recommend_hot','=',$is_recommend_hot]);
        }
        if(!empty($is_recommend_blast)){
            array_push($condition, ['is_recommend_blast','=',$is_recommend_blast]);
        }
        if(!empty($status)){
            array_push($condition, ['status','=',$status]);
        }
        if(!empty($name)){
            array_push($condition, ['name','like','%'.$name.'%']);
        }

        $list = GoodsModel::with(['goodsSpecification', 'goodsParameter'])
            ->where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        $goodsCategory = GoodsCategoryModel::column('name', 'id');
        foreach ($list as &$item){
            $item->first_goods_category_name = $goodsCategory[$item->first_goods_category_id] ?? '';
            $item->second_goods_category_name = $goodsCategory[$item->second_goods_category_id] ?? '';
            $item->third_goods_category_name = $goodsCategory[$item->third_goods_category_id] ?? '';
        }
        $total = GoodsModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加商品信息
     */
    public static function addGoodsInfo() :void
    {
        //$res = (new GoodsModel())->insertGetId(input('post.'));

        $info = GoodsModel::create(input('post.'));
        if(!$info->isEmpty()){
            //添加商品规格
            $goodsSpecificationList = input('post.goodsSpecificationList', []);
            foreach ($goodsSpecificationList as &$goodsSpecification){
                $goodsSpecification['goods_id'] = $info->id;
            }
            (new GoodsSpecificationModel())->saveAll($goodsSpecificationList);

            //添加商品参数
            $goodsParameterList = input('post.goodsParameterList', []);
            foreach ($goodsParameterList as &$goodsParameter){
                $goodsParameter['goods_id'] = $info->id;
            }
            (new GoodsParameterModel())->saveAll($goodsParameterList);
        }else{
            throw new BadRequestException(['errorMessage' => '失败']);
        }
    }

    /*
     * 修改商品信息
     */
    public static function editGoodsInfo() :void
    {
        $info = GoodsModel::findOrEmpty(input('post.id'));
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        $res = $info->save(input('post.'));
        if($res){
            //批量新增或更新商品规格
            $goodsSpecificationList = input('post.goodsSpecificationList', []);
            foreach ($goodsSpecificationList as &$goodsSpecification){
                $goodsSpecification['goods_id'] = $info->id;
            }
            (new GoodsSpecificationModel())->saveAll($goodsSpecificationList);

            //批量新增或更新商品参数
            $goodsParameterList = input('post.goodsParameterList', []);
            foreach ($goodsParameterList as &$goodsParameter){
                $goodsParameter['goods_id'] = $info->id;
            }
            (new GoodsParameterModel())->saveAll($goodsParameterList);
        }else{
            throw new BadRequestException(['errorMessage' => '失败']);
        }
    }

    /*
     * 删除商品信息
     */
    public static function deleteGoodsInfo() :void
    {
        $ids = explode(',', input('post.id'));
        foreach ($ids as $id){
            $info = GoodsModel::where('id', '=', $id)
                ->findOrEmpty();
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $count = OrderGoodsModel::where('goods_id', '=', $info->id)
                ->count();
            if($count) throw new BadRequestException(['errorMessage' => '存在订单商品']);
            $res = $info->delete();
            if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
        }
    }

    /*
     * 获取商品信息
     */
    public static function getGoodsInfo() :array
    {
        $info = GoodsModel::with(['goodsSpecification', 'goodsParameter'])
            ->where('id', '=', input('get.id', 0))
            ->findOrEmpty();
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        $goodsCategory = GoodsCategoryModel::column('name', 'id');
        $info->first_goods_category_name = $goodsCategory[$info->first_goods_category_id] ?? '';
        $info->second_goods_category_name = $goodsCategory[$info->second_goods_category_id] ?? '';
        $info->third_goods_category_name = $goodsCategory[$info->third_goods_category_id] ?? '';
        return $info->toArray();
    }

    /*
     * 获取商品分类列表
     */
    public static function getGoodsCategoryList() :array
    {
        $pid = input('get.pid', 0);
        $goodsCategory = GoodsCategoryModel::where('pid', '=', $pid)
            ->order(['sort'=>'asc','id'=>'desc'])
            ->select()
            ->toArray();
        return $goodsCategory;
    }
}