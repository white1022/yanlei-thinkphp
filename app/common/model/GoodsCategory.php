<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class GoodsCategory extends Base
{
    //模型关联
    /*//定义商品分类和商品一对多关联
    public function goods()
    {
        return $this->hasMany(Goods::class, 'goods_category_id', 'id');
    }*/
    //定义商品分类和楼层一对一关联
    public function storey()
    {
        return $this->hasOne(Storey::class, 'goods_category_id', 'id');
    }
}
