<?php
declare (strict_types = 1);

namespace app\common\validate;

class User extends Base
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
	protected $rule = [
        'id' => ['require'],
        'nickname' => ['require', 'min' => 2, 'max' => 8],
        'avatar' => ['require'],
        'email' => ['require', 'email'],
        'password' => ['require', 'min' => 5],
        'mobile' => ['require', 'mobile'],
        'name' => ['require', 'min' => 2],
        'is_use' => ['require', 'in' => '1,2'],
        'lang' => ['require', 'in' => 'zh-cn,en-us'],
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
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
        'add'  =>  ['nickname','avatar','email','mobile','password','name'],
        'edit'  =>  ['id','nickname','avatar','email','mobile','name'],
        'delete'  =>  ['id'],
        'is_use'  =>  ['id','is_use'],
    ];
}
