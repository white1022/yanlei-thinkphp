<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class ReadmeCategory extends Base
{
    //模型关联
    //定义文章分类和文章一对一关联
    public function readme()
    {
        return $this->hasOne(Readme::class, 'readme_category_id', 'id');
    }

}
