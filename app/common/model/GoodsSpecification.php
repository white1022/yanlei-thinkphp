<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class GoodsSpecification extends Base
{
    //模型关联
    //定义商品和商品规格一对多关联的相对关联
    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }
    //定义商品规格和订单商品一对多关联
    public function orderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'goods_specification_id', 'id');
    }
    //定义商品规格和购物车一对多关联
    public function cart()
    {
        return $this->hasMany(Cart::class, 'goods_specification_id', 'id');
    }
    //定义商品规格和评价一对多关联
    public function evaluate()
    {
        return $this->hasMany(Evaluate::class, 'goods_specification_id', 'id');
    }
}
