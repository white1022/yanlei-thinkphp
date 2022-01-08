<?php
declare (strict_types = 1);

namespace app\common\validate;

class Rule extends Base
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
	protected $rule = [
        'id' => ['require'],
        'pid' => ['require', 'number'],
        'name' => ['require'],
        'title' => ['require'],
        'icon' => ['require'],
        'jump' => [],
        'spread' => ['require'],
        'sort' => ['require', 'number'],
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
        'add'  =>  ['pid','title','icon','jump','spread','sort'],
        'edit'  =>  ['id','pid','title','icon','jump','spread','sort'],
        'delete'  =>  ['id'],
        'spread'  =>  ['id','spread'],
    ];
}
