<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class User extends Base
{
    //获取器
    public function getAvatarAttr($value, $data)
    {
        return $this->prefixUrl($value, $data);
    }
    public function getIsUseTextAttr($value, $data)
    {
        $is_use = [0=>'否',1=>'是'];
        return $is_use[$data['is_use']];
    }

    //修改器
    public function setLastLoginIpAttr($value, $data)
    {
        return $this->ipAddress($value, $data);
    }
    public function setPasswordAttr($value, $data)
    {
        return $this->hashEncryption($value, $data);
    }

    //搜索器
    public function searchNicknameAttr($query, $value, $data)
    {
        $query->where('nickname', 'like', '%' . $value . '%');
    }
    public function searchCreateTimeAttr($query, $value, $data)
    {
        $query->whereBetweenTime('create_time', $value[0], $value[1]);
    }

    //模型关联



}
