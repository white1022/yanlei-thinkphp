<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Address extends Base
{
    //模型关联
    //定义用户和地址一对多关联的相对关联
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    //定义地址和订单一对多关联
    public function order()
    {
        return $this->hasMany(Order::class, 'address_id', 'id');
    }
}
