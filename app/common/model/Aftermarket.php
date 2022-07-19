<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Aftermarket extends Base
{
    //模型关联
    //定义订单和售后一对一关联的相对关联
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

}
