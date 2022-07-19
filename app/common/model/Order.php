<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Order extends Base
{
    //模型关联
    //定义用户和订单一对多关联的相对关联
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    //定义地址和订单一对多关联的相对关联
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }
    //定义快递和订单一对多关联的相对关联
    public function express()
    {
        return $this->belongsTo(Express::class, 'express_id', 'id');
    }
    //定义订单和订单商品一对多关联
    public function orderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'order_no', 'order_no');
    }
    //定义订单和售后一对一关联
    public function aftermarket()
    {
        return $this->hasOne(Aftermarket::class, 'order_id', 'id');
    }
}
