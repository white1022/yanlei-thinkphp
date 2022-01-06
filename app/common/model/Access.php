<?php
declare (strict_types = 1);

namespace app\common\model;

use think\model\Pivot;

/**
 * @mixin \think\Model
 */
class Access extends Pivot
{
    //中间表模型开启时间戳字段自动写入
    protected $autoWriteTimestamp = true;
}
