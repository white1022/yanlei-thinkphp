<?php
declare (strict_types = 1);

namespace app\common\model;

/**
 * @mixin \think\Model
 */
class Role extends Base
{
    //定义管理员和角色多对多关联的相对关联
    public function admin()
    {
        return $this->belongsToMany(Admin::class, Access::class, 'admin_id', 'role_id');
    }
}
