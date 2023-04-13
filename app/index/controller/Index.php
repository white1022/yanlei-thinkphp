<?php
declare (strict_types = 1);

namespace app\index\controller;

use app\common\model\Goods as GoodsModel;
use app\common\model\GoodsCategory as GoodsCategoryModel;
use app\common\model\Slideshow as SlideshowModel;
use app\common\model\Storey as StoreyModel;
use think\response\Json;

class Index extends Base
{
    /*
     * 获取PC首页数据
     */
    public function pc() :Json
    {
        $data['slideshow'] = SlideshowModel::where('type', '=', 2)
            ->order(['sort'=>'asc','id'=>'desc'])->select();

        $goodsCategory = GoodsCategoryModel::order(['sort'=>'asc','id'=>'desc'])->select()->toArray();
        $data['goods_category'] = generateTree($goodsCategory);

        $data['storey'] = StoreyModel::order(['sort'=>'asc','id'=>'desc'])->select()->toArray();
        /*foreach ($data['storey'] as &$item1){
            foreach ($data['goods_category'] as $item2){
                if($item2['id'] == $item1['goods_category_id']){
                    $item1['goods_category'] = $item2;
                }
            }
        }*/
        foreach ($data['storey'] as &$item1){
            $item1['first_goods_category'] = [];
            $item1['second_goods_category_list'] = [];
            $item1['third_goods_category_list'] = [];

            $first_goods_category_id = 0;
            foreach ($goodsCategory as $item5){
                if($item5['id'] == $item1['goods_category_id']){
                    $item1['first_goods_category'] = $item5;
                    $first_goods_category_id = $item5['id'];
                }
            }

            $second_goods_category_ids = [];
            foreach ($goodsCategory as $item6){
                if($item6['pid'] == $first_goods_category_id){
                    $item1['second_goods_category_list'][] = $item6;
                    $second_goods_category_ids[] = $item6['id'];
                }
            }

            foreach ($goodsCategory as $item7){
                if(in_array($item7['pid'], $second_goods_category_ids)){
                    $item1['third_goods_category_list'][] = $item7;
                }
            }
        }

        $data['recommend_hot'] = [];
        $data['recommend_blast'] = [];
        $goods = GoodsModel::with([
            'goodsSpecification'
        ])->where([
            ['status', '=', 1],
        ])->order(['id'=>'desc'])->select()->toArray();
        foreach ($goods as $item3){
            if($item3['is_recommend_hot'] == 1){
                $data['recommend_hot'][] = $item3;
            }
            if($item3['is_recommend_blast'] == 1){
                $data['recommend_blast'][] = $item3;
            }

            foreach ($data['storey'] as &$item4){
                if($item4['goods_category_id'] == $item3['first_goods_category_id']){
                    if($item3['is_recommend_index'] == 1){
                        $item4['goods'][] = $item3;
                    }
                }
            }
        }

        return returnResponse(200, '成功', $data);
    }

    /*
     * 获取APP首页数据
     */
    public function app() :Json
    {
        $data['slideshow'] = SlideshowModel::where('type', '=', 1)
            ->order(['sort'=>'asc','id'=>'desc'])->select();

        $data['goods_category'] = GoodsCategoryModel::where('pid', '=', 0)
            ->order(['sort'=>'asc','id'=>'desc'])->select();

        $data['recommend_hot'] = [];
        $data['recommend_blast'] = [];
        $goods = GoodsModel::whereOr([
            [
                ['status', '=', 1],
                ['is_recommend_hot', '=', 1],
            ],
            [
                ['status', '=', 1],
                ['is_recommend_blast', '=', 1],
            ],
        ])->order(['id'=>'desc'])->select()->toArray();
        foreach ($goods as $item){
            if($item['is_recommend_hot'] == 1){
                $data['recommend_hot'][] = $item;
            }
            if($item['is_recommend_blast'] == 1){
                $data['recommend_blast'][] = $item;
            }
        }

        list($page, $limit) = get_page_limit();
        $data['goods']['list'] = GoodsModel::with(['goodsSpecification'])
            ->where('status', '=', 1)
            ->limit($limit)
            ->page($page)
            ->select();
        $data['goods']['total'] = GoodsModel::where('status', '=', 1)->count();

        return returnResponse(200, '成功', $data);
    }



}