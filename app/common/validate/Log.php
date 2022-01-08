<?php
declare (strict_types = 1);

namespace app\common\validate;

class Log extends Base
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
	protected $rule = [
        'id' => ['require'],
        'type' => ['require', 'in'=>'1,2'],
        'operator_id' => ['require', 'number'],
        'content' => ['require'],
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
     */
    protected $scene = [
        'delete'  =>  ['id'],
    ];
}
