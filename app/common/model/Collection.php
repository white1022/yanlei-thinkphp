<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Collection extends Base
{
    //模型关联
    //定义用户和收藏一对多关联的相对关联
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    //定义商品和收藏一对多关联的相对关联
    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }

}
