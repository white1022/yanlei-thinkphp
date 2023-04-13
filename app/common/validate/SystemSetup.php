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
        'site_logo' => ['require'],
        'site_copyright' => ['require'],
        'site_detail' => ['require'],
        'default_avatar' => ['require'],
        'platform_phone' => ['require'],
        'platform_wechat' => ['require'],
        'platform_application' => ['require'],
        'use_help' => ['require'],
        'about_platform' => ['require'],
        'platform_user_agreement' => ['require'],
        'platform_privacy_policy' => ['require'],
        'site_bottom_banner' => ['require'],
        'search_keyword' => ['require'],
        'goods_status' => ['require'],
        'refund_reason' => ['require'],
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
        'edit'  =>  ['site_name','site_logo','site_copyright','site_detail','default_avatar','platform_phone','platform_wechat','platform_application','use_help','about_platform','platform_user_agreement','platform_privacy_policy','site_bottom_banner','search_keyword','goods_status','refund_reason'],
    ];
}
