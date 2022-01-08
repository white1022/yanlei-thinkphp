<?php
declare (strict_types = 1);

namespace app\common\validate;

class SystemSetup extends Base
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
	protected $rule = [
        'id' => ['require'],
        'site_name' => ['require'],
        'site_icon' => ['require'],
        'site_copyright' => ['require'],
        'site_detail' => ['require'],
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
        'edit'  =>  ['site_name','site_icon','site_copyright','site_detail'],
    ];
}
