<?php
declare (strict_types = 1);

namespace app\common\validate;

use think\Validate;

class Aftermarket extends Base
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
        'order_id' => ['require', 'number'],
        'aftermarket_no' => ['require'],
        'refund_reason' => ['require'],
        'refund_remark' => ['require'],
        'status' => ['require', 'number'],
        'reject_reason' => [],
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
        'edit'  =>  ['id','status','reject_reason'],
        'delete'  =>  ['id'],
    ];
}
