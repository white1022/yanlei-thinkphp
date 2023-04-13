<?php
declare (strict_types = 1);

namespace app\index\controller;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Collection as CollectionModel;
use app\common\model\Goods as GoodsModel;
use app\common\model\GoodsCategory as GoodsCategoryModel;
use think\model\Relation;
use think\response\Json;

class Goods extends Base
{
    /*
     * 获取商品分类树
     */
    public function getGoodsCategoryTree() :Json
    {
        $goodsCategory = GoodsCategoryModel::order(['sort'=>'asc','id'=>'desc'])->select()->toArray();
        $list = generateTree($goodsCategory);
        return returnResponse(200, '成功', $list);
    }

    /*
     * 获取商品分类列表
     */
    public function getGoodsCategoryList() :Json
    {
        $pid = input('get.pid', '');

        $condition = [];

        if($pid != ''){
            array_push($condition, ['pid','=',$pid]);
        }

        $list = GoodsCategoryModel::where($condition)->order(['sort'=>'asc','id'=>'desc'])->select();

        return returnResponse(200, '成功', $list);
    }

    /*
     * 获取商品列表
     */
    public function getGoodsList() :Json
    {
        list($page, $limit) = get_page_limit();
        $first_goods_category_id = input('get.first_goods_category_id', '');
        $second_goods_category_id = input('get.second_goods_category_id', '');
        $third_goods_category_id = input('get.third_goods_category_id', '');
        $name = input('get.name', '');
        $is_recommend_index = input('get.is_recommend_index', '');
        $is_recommend_hot = input('get.is_recommend_hot', '');
        $is_recommend_blast = input('get.is_recommend_blast', '');
        $sort = input('get.sort', '');
        $user_id = input('get.user_id', 0);

        $condition = [
            ['status','=',1],
        ];
        if(!empty($first_goods_category_id)){
            array_push($condition, ['first_goods_category_id','=',$first_goods_category_id]);
        }
        if(!empty($second_goods_category_id)){
            array_push($condition, ['second_goods_category_id','=',$second_goods_category_id]);
        }
        if(!empty($third_goods_category_id)){
            array_push($condition, ['third_goods_category_id','=',$third_goods_category_id]);
        }
        if(!empty($name)){
            array_push($condition, ['name','like','%'.$name.'%']);
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

        $query = GoodsModel::with([
            'goodsSpecification',
        ])->withCount([
            'cart' => function(Relation $query) use ($user_id) {
                $query->where('user_id', '=', $user_id);
            }
        ])->where($condition)
            ->limit($limit)
            ->page($page);
        switch ($sort)
        {
            case 'sales_desc':
                //销量倒序
                $query->order(['sales'=>'desc','create_time'=>'desc']);
                break;
            case 'price_desc':
                //价格倒序
                $query->order(['price'=>'desc','create_time'=>'desc']);
                break;
            case 'popularity_desc':
                //人气倒序
                $query->order(['popularity'=>'desc','create_time'=>'desc']);
                break;
            case 'update_desc':
                //更新倒序
                $query->order(['update_time'=>'desc','create_time'=>'desc']);
                break;
            default:
                //默认排序
                $query->order('create_time', 'desc');
        }

        $list = $query->select();
        $total = GoodsModel::where($condition)->count();

        return returnResponse(200, '成功', [
            'list' => $list,
            'total' => $total,
        ]);
    }

    /*
     * 获取商品详情
     */
    public function getGoodsInfo() :Json
    {
        $id = input('get.id', 0);
        $info = GoodsModel::with([
            'goodsSpecification',
            'goodsParameter',
            'evaluate' => function(Relation $query) {
                $query->with(['user'])->order('create_time', 'desc')->withLimit(4);
            },
        ])->withCount(['evaluate'])
            ->findOrEmpty($id);
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '商品不存在']);

        //用户是否收藏
        $count = CollectionModel::where([
            ['user_id', '=', input('get.user_id', 0)],
            ['goods_id', '=', $info->id],
        ])->count();
        $info->is_collection = (bool)$count;

        //猜你喜欢，显示同类商品
        $info->guessYouLike = GoodsModel::where([
            ['first_goods_category_id', '=', $info->first_goods_category_id],
            ['second_goods_category_id', '=', $info->second_goods_category_id],
            ['third_goods_category_id', '=', $info->third_goods_category_id],
            ['id', '<>', $info->id],
            ['status', '=', 1],
        ])->order('create_time', 'desc')
            ->limit(4)
            ->select();

        //精品推荐，显示推荐商品
        $info->boutiqueRecommendation = GoodsModel::where([
            ['is_recommend_index', '=', 1],
            ['status', '=', 1],
        ])->order('create_time', 'desc')
            ->limit(4)
            ->select();

        return returnResponse(200, '成功', $info);
    }




}