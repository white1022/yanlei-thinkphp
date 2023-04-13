<?php
declare (strict_types = 1);

namespace app\common\validate;

use think\Validate;

class Express extends Base
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id' => ['require'],
        'name' => ['require'],
        'code' => ['require'],
        'logo' => ['require'],
        'phone' => ['require'],
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
        'add'  =>  ['name','code','logo','phone'],
        'edit'  =>  ['id','name','code','logo','phone'],
        'delete'  =>  ['id'],
    ];
}
