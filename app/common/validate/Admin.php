<?php
declare (strict_types = 1);

namespace app\common\validate;

use think\Validate;

class Admin extends Base
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
	protected $rule = [
        'id' => ['require', 'num'=>'number'],
        'nickname' => ['require', 'min' => 2, 'max' => 8],
        'avatar' => ['require'],
        'email' => ['require', 'email'=>'email'],
        'password' => ['require', 'min' => 5],
        'mobile' => ['require', 'mobile'=>'mobile'],
        'name' => ['require', 'min' => 2],
        'is_use' => ['require', 'in'=>'0,1'],
        'lang' => ['require', 'in'=>'zh-cn,en-us'],
        'role_id' => ['require', 'num'=>'number'], //用于新增中间表数据
        'reset_password' => ['min' => 5], //用于重置密码
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [

    ];

    /**
     * 验证场景
     *
     * @var array
     */
    protected $scene = [
        'login'  =>  ['email','password'],
        'lang'  =>  ['lang'],
        'add'  =>  ['email','password','role_id'],
        'edit'  =>  ['id','nickname','avatar','email','password'],
        'delete'  =>  ['id'],
        'is_use'  =>  ['id','is_use'],
        'account'  =>  ['id','nickname','avatar','email','mobile','reset_password','name'],
    ];
}
