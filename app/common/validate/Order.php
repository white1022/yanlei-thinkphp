<?php
declare (strict_types = 1);

namespace app\common\validate;

use think\Validate;

class Order extends Base
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
        'address_id' => ['require', 'number'],
        'express_id' => ['require', 'number'],
        'order_no' => ['require'],
        'express_no' => ['require'],
        'payment' => ['require', 'number'],
        'amount' => ['require'],
        'freight' => ['require'],
        'remark' => ['require'],
        'is_remind_delivery' => ['require', 'in' => '1,2'],
        'payment_time' => ['require', 'number'],
        'delivery_time' => ['require', 'number'],
        'receipt_time' => ['require', 'number'],
        'cancel_time' => ['require', 'number'],
        'status' => ['require', 'number'],
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
        'delivery'  =>  ['id','express_id','express_no'],
    ];
}
