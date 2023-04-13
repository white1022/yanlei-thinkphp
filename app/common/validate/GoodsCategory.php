<?php
declare (strict_types = 1);

namespace app\common\validate;

use think\Validate;

class GoodsCategory extends Base
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id' => ['require'],
        'pid' => ['require', 'number'],
        'name' => ['require'],
        'image' => ['require'],
        'sort' => ['require', 'number'],
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];

    /**
     * 验证场景
     *
     * @var array
     */
    protected $scene = [
        'add'  =>  ['pid','name','image','sort'],
        'edit'  =>  ['id','pid','name','image','sort'],
        'delete'  =>  ['id'],
    ];
}
