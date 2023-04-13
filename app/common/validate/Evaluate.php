<?php
declare (strict_types = 1);

namespace app\common\validate;

use think\Validate;

class Evaluate extends Base
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id' => ['require'],
        'order_id' => ['require', 'number'],
        'order_goods_id' => ['require', 'number'],
        'user_id' => ['require', 'number'],
        'goods_id' => ['require', 'number'],
        'quantity' => ['require', 'number'],
        'attribute' => ['require'],
        'goods_specification_id' => ['require', 'number'],
        'score' => ['require'],
        'image' => ['require'],
        'content' => ['require'],
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
