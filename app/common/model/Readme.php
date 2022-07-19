<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Readme extends Base
{
    //模型关联
    //定义文章分类和文章一对一关联的相对关联
    public function readmeCategory()
    {
        return $this->belongsTo(ReadmeCategory::class, 'readme_category_id', 'id');
    }
}
