<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class OrderGoods extends Base
{
    //模型关联
    //定义订单和订单商品一对多关联的相对关联
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_no', 'order_no');
    }
    //定义商品和订单商品一对多关联的相对关联
    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }
    //定义商品规格和订单商品一对多关联的相对关联
    public function goodsSpecification()
    {
        return $this->belongsTo(GoodsSpecification::class, 'goods_specification_id', 'id');
    }
    //定义订单商品和评价一对一关联
    public function evaluate()
    {
        return $this->hasOne(Evaluate::class, 'order_goods_id', 'id');
    }
}
