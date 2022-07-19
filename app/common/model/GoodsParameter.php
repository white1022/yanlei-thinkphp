<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class GoodsParameter extends Base
{
    //模型关联
    //定义商品和商品参数一对多关联的相对关联
    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }
}
