<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Evaluate extends Base
{
    //模型关联
    //定义订单商品和评价一对一关联的相对关联
    public function orderGoods()
    {
        return $this->belongsTo(OrderGoods::class, 'order_goods_id', 'id');
    }
    //定义用户和评价一对多关联的相对关联
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    //定义商品和评价一对多关联的相对关联
    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }
    //定义商品规格和评价一对多关联的相对关联
    public function goodsSpecification()
    {
        return $this->belongsTo(GoodsSpecification::class, 'goods_specification_id', 'id');
    }

}
