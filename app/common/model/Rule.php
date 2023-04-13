<?php
declare (strict_types = 1);

namespace app\common\model;

/**
 * @mixin \think\Model
 */
class Rule extends Base
{
    //获取器
    public function getSpreadAttr($value,$data)
    {
        $spread = [1=>true,2=>false];
        return $spread[$data['spread']];
    }

    //修改器
    public function setSpreadAttr($value, $data)
    {
        return $value == 'true' ? 1 : 2;
    }
}
