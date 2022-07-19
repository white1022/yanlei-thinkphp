<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Express extends Base
{
    //模型关联
    //定义快递和订单一对多关联
    public function order()
    {
        return $this->hasMany(Order::class, 'express_id', 'id');
    }

}
