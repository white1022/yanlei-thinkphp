<?php
declare (strict_types = 1);

namespace app\common\validate;

use think\Validate;

class Address extends Base
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id' => ['require'],
        'user_id' => ['require', 'number'],
        'receiver' => ['require'],
        'mobile' => ['require'],
        'province' => ['require'],
        'city' => ['require'],
        'area' => ['require'],
        'address' => ['require'],
        'is_default' => ['require', 'in' => '1,2'],
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
        'delete'  =>  ['id'],
    ];
}
