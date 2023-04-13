<?php
declare (strict_types = 1);

namespace app\common\validate;

class Role extends Base
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
	protected $rule = [
        'id' => ['require'],
        'name' => ['require'],
        'rules' => ['require'],
        'is_use' => ['require', 'in'=>'1,2'],
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
        'add'  =>  ['name','rules','is_use'],
        'edit'  =>  ['id','name','rules','is_use'],
        'delete'  =>  ['id'],
    ];
}
