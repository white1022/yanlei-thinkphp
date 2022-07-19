<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Feedback extends Base
{
    //模型关联
    //定义用户和意见反馈一对多关联的相对关联
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
