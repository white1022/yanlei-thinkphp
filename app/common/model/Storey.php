<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Storey extends Base
{
    //模型关联
    //定义商品分类和楼层一对一关联的相对关联
    public function goodsCategory()
    {
        return $this->belongsTo(GoodsCategory::class, 'goods_category_id', 'id');
    }

}
