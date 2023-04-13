<?php
declare (strict_types = 1);

namespace app\common\model;

/**
 * @mixin \think\Model
 */
class User extends Base
{
    //获取器
    /*public function getAvatarAttr($value, $data)
    {
        return $this->prefixUrl($value, $data);
    }*/

    //修改器
    /*public function setAvatarAttr($value, $data)
    {
        return $this->originUrl($value, $data);
    }*/
    public function setLastLoginIpAttr($value, $data)
    {
        return $this->ipAddress($value, $data);
    }
    public function setPasswordAttr($value, $data)
    {
        return $this->hashEncryption($value, $data);
    }

    //搜索器

    //模型关联
    //定义用户和意见反馈一对多关联
    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'user_id', 'id');
    }
    //定义用户和交易一对多关联
    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'id');
    }
    //定义用户和地址一对多关联
    public function address()
    {
        return $this->hasMany(Address::class, 'user_id', 'id');
    }
    //定义用户和订单一对多关联
    public function order()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }
    //定义用户和收藏一对多关联
    public function collection()
    {
        return $this->hasMany(Collection::class, 'user_id', 'id');
    }
    //定义用户和购物车一对多关联
    public function cart()
    {
        return $this->hasMany(Cart::class, 'user_id', 'id');
    }
    //定义用户和评价一对多关联
    public function evaluate()
    {
        return $this->hasMany(Evaluate::class, 'user_id', 'id');
    }


}
