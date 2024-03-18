<?php
declare (strict_types = 1);

namespace app\common\validate;

class Admin extends Base
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
        'introduction' => ['require', 'min' => 15, 'max' => 200],
        'is_use' => ['require', 'in' => '1,2'],
        'lang' => ['require', 'in' => 'zh-cn,en-us'],
        'role' => ['require', 'array'], //用于中间表数据
        'confirm_password' => ['require', 'min' => 5, 'confirm'=> 'password'], //用于确认密码
        'old_password' => ['require', 'min' => 5], //用于修改密码，拿旧密码换新密码
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
        'login'  =>  ['email','password'],
        'lang'  =>  ['lang'],
        'add'  =>  ['nickname','avatar','email','mobile','password','name','is_use','role'],
        'edit'  =>  ['id','nickname','avatar','email','mobile','name','is_use','role'],
        'delete'  =>  ['id'],
        'is_use'  =>  ['id','is_use'],
        'password'  =>  ['id','password', 'confirm_password'],
        'my_profile'  =>  ['nickname','avatar','email','mobile','name'],
        'my_password'  =>  ['old_password','password','confirm_password'],
    ];
}
