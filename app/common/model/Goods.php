<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Goods extends Base
{
    //模型关联
    /*//定义商品分类和商品一对多关联的相对关联
    public function goodsCategory()
    {
        return $this->belongsTo(GoodsCategory::class, 'goods_category_id', 'id');
    }*/
    //定义商品和商品规格一对多关联
    public function goodsSpecification()
    {
        return $this->hasMany(GoodsSpecification::class, 'goods_id', 'id');
    }
    //定义商品和商品参数一对多关联
    public function goodsParameter()
    {
        return $this->hasMany(GoodsParameter::class, 'goods_id', 'id');
    }
    //定义商品和订单商品一对多关联
    public function orderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'goods_id', 'id');
    }
    //定义商品和收藏一对多关联
    public function collection()
    {
        return $this->hasMany(Collection::class, 'goods_id', 'id');
    }
    //定义商品和购物车一对多关联
    public function cart()
    {
        return $this->hasMany(Cart::class, 'goods_id', 'id');
    }
    //定义商品和评价一对多关联
    public function evaluate()
    {
        return $this->hasMany(Evaluate::class, 'goods_id', 'id');
    }
}
