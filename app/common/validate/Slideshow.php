<?php
declare (strict_types = 1);

namespace app\common\validate;

use think\Validate;

class Slideshow extends Base
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id' => ['require'],
        'type' => ['require', 'in' => '1,2'],
        'name' => ['require'],
        'image' => ['require'],
        'link' => ['require'],
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
        'add'  =>  ['type','name','image','sort'],
        'edit'  =>  ['id','type','name','image','sort'],
        'delete'  =>  ['id'],
    ];
}
