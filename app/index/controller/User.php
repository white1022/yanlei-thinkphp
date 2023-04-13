<?php
declare (strict_types = 1);

namespace app\index\controller;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Address as AddressModel;
use app\common\model\Captcha as CaptchaModel;
use app\common\model\Cart as CartModel;
use app\common\model\Collection as CollectionModel;
use app\common\model\Evaluate as EvaluateModel;
use app\common\model\Feedback as FeedbackModel;
use app\common\model\Order as OrderModel;
use app\common\model\OrderGoods as OrderGoodsModel;
use app\common\model\SystemSetup as SystemSetupModel;
use app\common\model\Transaction as TransactionModel;
use app\common\model\Aftermarket as AftermarketModel;
use app\common\model\Notice as NoticeModel;
use app\common\model\Goods as GoodsModel;
use app\common\model\GoodsSpecification as GoodsSpecificationModel;
use app\common\model\User as UserModel;
use app\common\service\Jwt as JwtService;
use app\common\service\Log as LogService;
use app\common\service\Redis as RedisService;
use app\common\service\User as UserService;
use app\common\service\WeChatJsSdk as WeChatJsSdkService;
use think\facade\Config;
use think\response\Json;

class User extends Base
{
    /*
     * 用户注册
     */
    public function register() :Json
    {
        $mobile = input('post.mobile', '');
        $password = input('post.password', '');
        $captcha = input('post.captcha', '');

        if(empty($mobile)) throw new BadRequestException(['errorMessage' => '手机号不能为空']);
        if(empty($password) || strlen($password) < 5) throw new BadRequestException(['errorMessage' => '密码长度不能小于5']);
        $user = UserModel::where('mobile', '=', $mobile)->findOrEmpty();
        if(!$user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户手机号已存在']);

        //验证验证码
        $captchaInfo = CaptchaModel::where([
            ['mobile', '=', $mobile],
            ['expire_time', '>', time()],
        ])->order(['id' => 'desc'])
            ->findOrEmpty();
        if($captchaInfo->isEmpty()) throw new BadRequestException(['errorMessage' => '验证码不存在或已过期']);
        if($captcha != $captchaInfo->captcha) throw new BadRequestException(['errorMessage' => '验证码不正确']);

        $user = UserModel::create([
            'mobile'  =>  $mobile,
            'password'  =>  $password,
            'nickname'  =>  random_string(5, 2),
            'avatar'  =>  SystemSetupModel::where('id', '=', 1)->value('default_avatar'),
            'level'  =>  1,
            'is_use'  =>  1,
        ]);

        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '失败']);

        //更新验证码
        $captchaInfo->expire_time = time();
        $captchaInfo->save();

        //返回token
        $token = UserService::getUserTokenById((int)$user->id);
        LogService::save(2, (int)$user->id, '登入');
        return returnResponse(200, '成功', ['token' => $token]);
    }

    /*
     * 忘记密码
     */
    public function forgetPassword() :Json
    {
        $mobile = input('post.mobile', '');
        $password = input('post.password', '');
        $captcha = input('post.captcha', '');

        if(empty($mobile)) throw new BadRequestException(['errorMessage' => '手机号不能为空']);
        if(empty($password) || strlen($password) < 5) throw new BadRequestException(['errorMessage' => '密码长度不能小于5']);

        $user = UserModel::where('mobile', '=', $mobile)->findOrEmpty();
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户手机号不存在']);

        //验证验证码
        $captchaInfo = CaptchaModel::where([
            ['mobile', '=', $mobile],
            ['expire_time', '>', time()],
        ])->order(['id' => 'desc'])
            ->findOrEmpty();
        if($captchaInfo->isEmpty()) throw new BadRequestException(['errorMessage' => '验证码不存在或已过期']);
        if($captcha != $captchaInfo->captcha) throw new BadRequestException(['errorMessage' => '验证码不正确']);

        $user->password = $password;
        $res = $user->save();

        if($res){
            //更新验证码
            $captchaInfo->expire_time = time();
            $captchaInfo->save();

            //返回token
            $token = UserService::getUserTokenById($user->id);
            LogService::save(2, $user->id, '登入');
            return returnResponse(200, '成功', ['token' => $token]);
        }else{
            throw new BadRequestException(['errorMessage' => '失败']);
        }
    }

    /*
     * 实名认证
     * https://market.aliyun.com/products/57002003/cmapi00035152.html?spm=5176.730005.result.6.6f343524LqhRqE&innerSource=search_%E5%AE%9E%E5%90%8D%E8%AE%A4%E8%AF%81#sku=yuncode29152000014
     */
    public function realNameAuthentication() :Json
    {
        $name = input('post.name', '');
        $identity_card = input('post.identity_card', '');

        if(empty($name)) throw new BadRequestException(['errorMessage' => '姓名不能为空']);
        if(empty($identity_card)) throw new BadRequestException(['errorMessage' => '身份证号不能为空']);

        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        //请求实名认证接口
        $url = 'http://checkone.market.alicloudapi.com/chinadatapay/1882';
        $method = 'POST';
        $header = [
            'Authorization:APPCODE ' . Config::get('config.certification.AppCode'),
            'Content-Type:application/x-www-form-urlencoded; charset=UTF-8',
        ];
        $param = [
            'idcard' => $identity_card,
            'name' => $name,
        ];
        $res = curl($url, $method, $header, $param);

        if($res && $res['code'] == 10000 && $res['data']['result'] == 1){
            $user->name = $name;
            $user->identity_card = $identity_card;
            $res = $user->save();
            if($res){
                return returnResponse(200, '成功', []);
            }else{
                throw new BadRequestException(['errorMessage' => '保存失败']);
            }
        }else{
            throw new BadRequestException(['errorMessage' => '认证失败']);
        }
    }

    /*
     * 物流查询
     * https://market.aliyun.com/products/57126001/cmapi021863.html?spm=5176.730005.result.8.37903524KpfVQJ&innerSource=search_%E5%BF%AB%E9%80%92#sku=yuncode1586300000
     */
    public function logisticsQuery() :Json
    {
        $no = input('get.no', '');

        if(empty($no)) throw new BadRequestException(['errorMessage' => '快递单号不能为空']);

        //请求物流查询接口
        $url = 'https://wuliu.market.alicloudapi.com/kdi';
        $method = 'GET';
        $header = [
            'Authorization:APPCODE ' . Config::get('config.logisticsquery.AppCode'),
        ];
        $param = [
            'no' => $no,
        ];
        $res = curl($url, $method, $header, $param);

        if($res && $res['status'] == 0){
            return returnResponse(200, '成功', $res);
        }else{
            throw new BadRequestException(['errorMessage' => '查询失败']);
        }
    }

    /*
     * 获取个人信息
     */
    public function getPersonalInformation() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);
        return returnResponse(200, '成功', $user);
    }

    /*
     * 修改个人信息
     */
    public function editPersonalInformation() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $nickname = input('post.nickname', '');
        $avatar = input('post.avatar', '');
        $sex = input('post.sex', '');
        $birthday = input('post.birthday', '');

        $nickname && $user->nickname = $nickname;
        $avatar && $user->avatar = $avatar;
        $sex != '' && $user->sex = $sex;
        $birthday && $user->birthday = $birthday;

        if(!$user->save()) throw new BadRequestException(['errorMessage' => '失败']);

        return returnResponse(200, '成功', []);
    }

    /*
     * 注销账号
     */
    public function cancelAccount() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);
        if(!$user->delete()) throw new BadRequestException(['errorMessage' => '失败']);

        // TODO 其他数据的清理
        AddressModel::where('user_id', '=', $user->id)->delete();

        return returnResponse(200, '成功', []);
    }

    /*
     * 更换手机号
     */
    public function changeMobile() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $mobile = input('post.mobile', '');
        $captcha = input('post.captcha', '');

        if(empty($mobile)) throw new BadRequestException(['errorMessage' => '手机号不能为空']);
        $count = UserModel::where('mobile', '=', $mobile)->count();
        if($count) throw new BadRequestException(['errorMessage' => '用户手机号已存在']);

        //验证验证码
        $captchaInfo = CaptchaModel::where([
            ['mobile', '=', $mobile],
            ['expire_time', '>', time()],
        ])->order(['id' => 'desc'])
            ->findOrEmpty();
        if($captchaInfo->isEmpty()) throw new BadRequestException(['errorMessage' => '验证码不存在或已过期']);
        if($captcha != $captchaInfo->captcha) throw new BadRequestException(['errorMessage' => '验证码不正确']);

        $user->mobile = $mobile;
        $res = $user->save();

        if($res){
            //更新验证码
            $captchaInfo->expire_time = time();
            $captchaInfo->save();
            return returnResponse(200, '成功', []);
        }else{
            throw new BadRequestException(['errorMessage' => '失败']);
        }
    }

    /*
    * 检测令牌是否过期
    */
    public function checkTokenExpire() :Json
    {
        $token = input('post.token', '');
        if(empty($token)) throw new BadRequestException(['errorMessage' => '令牌不能为空']);
        // 验证token有效性
        $jwt = JwtService::getInstance();
        $jwt->setToken($token);
        $is_expire = (!$jwt->validate() || !$jwt->verify()) ? true : false;
        return returnResponse(200, '成功', ['is_expire' => $is_expire]);
    }

    /*
     * 获取交易列表
     */
    public function getTransactionList() :Json
    {
        list($page, $limit) = get_page_limit();

        $condition = [
            ['user_id','=',$this->userId],
        ];

        $list = TransactionModel::where($condition)
            ->limit($limit)
            ->page($page)
            ->order(['create_time' => 'desc'])
            ->select();
        $total = TransactionModel::where($condition)->count();

        return returnResponse(200, '成功', [
            'list' => $list,
            'total' => $total,
        ]);
    }

    /*
     * 获取地址列表
     */
    public function getAddressList() :Json
    {
        list($page, $limit) = get_page_limit();

        $condition = [
            ['user_id','=',$this->userId],
        ];

        $list = AddressModel::where($condition)
            ->limit($limit)
            ->page($page)
            ->order(['create_time' => 'desc'])
            ->select();
        $total = AddressModel::where($condition)->count();

        return returnResponse(200, '成功', [
            'list' => $list,
            'total' => $total,
        ]);
    }

    /*
     * 添加/修改 地址
     */
    public function addEditAddress() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $data = input('post.');

        //默认地址只能存在一个
        if($data['is_default'] == 1){
            AddressModel::where([
                ['user_id', '=', $user->id],
            ])->update(['is_default' => 2]);
        }

        if(isset($data['id'])){
            $info = AddressModel::where([
                ['user_id', '=', $user->id],
                ['id', '=', $data['id']],
            ])->findOrEmpty();
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '地址不存在']);
            $res = $info->save($data);
        }else{
            $data['user_id'] = $user->id;
            $res = (new AddressModel())->save($data);
        }
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);

        return returnResponse(200, '成功', []);
    }

    /*
    * 删除地址
    */
    public function deleteAddress() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $ids = explode(',', input('post.id', ''));
        foreach ($ids as $id){
            $address = AddressModel::where([
                ['user_id', '=', $user->id],
                ['id', '=', $id],
            ])->findOrEmpty();
            if($address->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);

            $count = OrderModel::where('address_id', '=', $address->id)
                ->count();
            if($count) throw new BadRequestException(['errorMessage' => '存在订单使用该地址']);

            if(!$address->delete()) throw new BadRequestException(['errorMessage' => '失败']);
        }
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取默认地址
     */
    public function getDefaultAddress() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $address = AddressModel::where([
            ['user_id', '=', $this->userId],
            ['is_default', '=', 1],
        ])->findOrEmpty();

        return returnResponse(200, '成功', $address);
    }

    /*
     * 获取收藏列表
     */
    public function getCollectionList() :Json
    {
        list($page, $limit) = get_page_limit();

        $condition = [
            ['user_id','=',$this->userId],
        ];

        $list = CollectionModel::with([
            'user',
            'goods'
        ])->where($condition)
            ->limit($limit)
            ->page($page)
            ->order(['create_time' => 'desc'])
            ->select();
        $total = CollectionModel::where($condition)->count();

        return returnResponse(200, '成功', [
            'list' => $list,
            'total' => $total,
        ]);
    }

    /*
     * 是否收藏
     */
    public function isCollection() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $count = CollectionModel::where([
            'user_id' => $user->id,
            'goods_id' => input('post.goods_id', 0),
        ])->count();
        return returnResponse(200, '成功', [
            'collection' => (bool)$count,
        ]);
    }

    /*
     * 添加收藏
     */
    public function addCollection() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $goods_id = input('post.goods_id', 0);

        $collection = CollectionModel::where([
            'user_id' => $user->id,
            'goods_id' => $goods_id,
        ])->findOrEmpty();
        if(!$collection->isEmpty()) throw new BadRequestException(['errorMessage' => '收藏已存在']);

        $info = CollectionModel::create([
            'user_id' => $user->id,
            'goods_id' => $goods_id,
        ]);

        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '失败']);
        return returnResponse(200, '成功', []);
    }

    /*
    * 删除收藏
    */
    public function deleteCollection() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $id = input('post.id', '');
        $res = CollectionModel::where([
            ['user_id', '=', $user->id],
            ['id', 'in', explode(',', $id)],
        ])->delete();
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
        return returnResponse(200, '成功', []);
    }

    /*
    * 取消收藏
    */
    public function cancelCollection() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $collection = CollectionModel::where([
            ['user_id', '=', $user->id],
            ['goods_id', '=', input('post.goods_id', 0)],
        ])->findOrEmpty();
        if($collection->isEmpty()) throw new BadRequestException(['errorMessage' => '收藏不存在']);
        $res = $collection->delete();
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
        return returnResponse(200, '成功', []);
    }

    /*
     * 添加意见反馈
     */
    public function addFeedback() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $info = FeedbackModel::create([
            'user_id' => $user->id,
            'name' => input('post.name', ''),
            'mobile' => input('post.mobile', ''),
            'image' => input('post.image', ''),
            'content' => input('post.content', ''),
            'status' => 2,
        ]);

        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '失败']);
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取购物车列表
     */
    public function getCartList() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        list($page, $limit) = get_page_limit();

        $condition = [
            ['user_id','=',$user->id],
        ];

        $list = CartModel::with([
            'user',
            'goods',
            'goodsSpecification',
        ])->where($condition)
            ->limit($limit)
            ->page($page)
            ->order(['create_time' => 'desc'])
            ->select();
        $total = CartModel::where($condition)->count();

        return returnResponse(200, '成功', [
            'list' => $list,
            'total' => $total,
        ]);
    }

    /*
     * 添加购物车
     */
    public function addCart() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $cart = CartModel::where([
            ['user_id', '=', $user->id],
            ['goods_id', '=', input('post.goods_id', 0)],
            ['attribute', '=', input('post.attribute', '')],
            ['goods_specification_id', '=', input('post.goods_specification_id', 0)],
        ])->findOrEmpty();

        //判断是否存在
        if($cart->isEmpty()){
            $info = CartModel::create([
                'user_id' => $user->id,
                'goods_id' => input('post.goods_id', 0),
                'quantity' => input('post.quantity', 0),
                'attribute' => input('post.attribute', ''),
                'goods_specification_id' => input('post.goods_specification_id', 0),
            ]);
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '失败']);
        }else{
            throw new BadRequestException(['errorMessage' => '请勿重复添加购物车']);
        }

        return returnResponse(200, '成功', []);
    }

    /*
    * 删除购物车
    */
    public function deleteCart() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $id = input('post.id', '');
        $res = CartModel::where([
            ['user_id', '=', $user->id],
            ['id', 'in', explode(',', $id)],
        ])->delete();
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
        return returnResponse(200, '成功', []);
    }

    /*
    * 修改购物车数量
    */
    public function modifyCartQuantity() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $cart = CartModel::where([
            ['user_id', '=', $user->id],
            ['id', '=', input('post.id', 0)],
        ])->findOrEmpty();
        if($cart->isEmpty()) throw new BadRequestException(['errorMessage' => '购物车不存在']);
        $cart->quantity = input('post.quantity', 0);
        if(!$cart->save()) throw new BadRequestException(['errorMessage' => '失败']);
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取购物车列表通过ID
     */
    public function getCartListById() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $id = input('get.id', '');

        $condition = [
            ['user_id','=',$user->id],
            ['id', 'in', explode(',', $id)],
        ];

        $list = CartModel::with([
            'user',
            'goods',
            'goodsSpecification',
        ])->where($condition)
            ->order(['create_time' => 'desc'])
            ->select();

        return returnResponse(200, '成功', $list);
    }

    /*
     * 在小程序用户立即下单时构造临时购物车数据
     */
    public function getConstructionTemporaryCartData() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $quantity = input('post.quantity', 0);
        $attribute = input('post.attribute', '');
        $goods_id = input('post.goods_id', 0);
        $goods_specification_id = input('post.goods_specification_id', 0);

        $goods = GoodsModel::findOrEmpty($goods_id);
        if($goods->isEmpty()) throw new BadRequestException(['errorMessage' => '商品不存在']);
        $goodsSpecification = GoodsSpecificationModel::findOrEmpty($goods_specification_id);
        if($goodsSpecification->isEmpty()) throw new BadRequestException(['errorMessage' => '商品规格不存在']);

        $data = [
            'quantity' => $quantity,
            'attribute' => $attribute,
            'user' => $user,
            'goods' => $goods,
            'goodsSpecification' => $goodsSpecification,
        ];

        return returnResponse(200, '成功', $data);
    }


    /*
     * 获取订单列表
     */
    public function getOrderList() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        list($page, $limit) = get_page_limit();
        $order_no = input('get.order_no', '');
        $status = input('get.status', '');

        $condition = [
            ['user_id', '=', $user->id],
        ];
        if(!empty($order_no)){
            array_push($condition, ['order_no','=',$order_no]);
        }
        if(!empty($status)){
            array_push($condition, ['status','in',explode(',', $status)]);
        }

        $list = OrderModel::with([
            'user',
            'address',
            'express',
            'orderGoods' => [
                'goodsSpecification',
                'goods' => [
                    'goodsSpecification',
                    'goodsParameter'
                ]
            ]
        ])->where($condition)
            ->limit($limit)
            ->page($page)
            ->order(['create_time' => 'desc'])
            ->select();
        $total = OrderModel::where($condition)->count();

        return returnResponse(200, '成功', [
            'list' => $list,
            'total' => $total,
        ]);
    }

    /*
     * 提醒发货
     */
    public function remindDelivery() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);
        $order = OrderModel::where([
            ['user_id', '=', $user->id],
            ['id', '=', input('post.id', 0)],
        ])->findOrEmpty();
        if($order->isEmpty()) throw new BadRequestException(['errorMessage' => '订单不存在']);
        if($order->is_remind_delivery == 1) throw new BadRequestException(['errorMessage' => '请勿重复提醒']);
        if($order->status != 2) throw new BadRequestException(['errorMessage' => '订单不是待发货状态']);
        $order->is_remind_delivery = 1;
        if(!$order->save()) throw new BadRequestException(['errorMessage' => '失败']);
        return returnResponse(200, '成功', []);
    }

    /*
     * 确认收货
     */
    public function confirmReceipt() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);
        $order = OrderModel::where([
            ['user_id', '=', $user->id],
            ['id', '=', input('post.id', 0)],
        ])->findOrEmpty();
        if($order->isEmpty()) throw new BadRequestException(['errorMessage' => '订单不存在']);
        if($order->status != 3) throw new BadRequestException(['errorMessage' => '订单不是待收货状态']);
        $order->receipt_time = time();
        $order->status = 4;
        if(!$order->save()) throw new BadRequestException(['errorMessage' => '失败']);
        return returnResponse(200, '成功', []);
    }

    /*
     * 取消订单
     */
    public function cancelOrder() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);
        $order = OrderModel::where([
            ['user_id', '=', $user->id],
            ['id', '=', input('post.id', 0)],
        ])->findOrEmpty();
        if($order->isEmpty()) throw new BadRequestException(['errorMessage' => '订单不存在']);
        if($order->status != 1) throw new BadRequestException(['errorMessage' => '订单不是待付款状态']);
        $order->cancel_time = time();
        $order->status = 6;
        if(!$order->save()) throw new BadRequestException(['errorMessage' => '失败']);
        return returnResponse(200, '成功', []);
    }

    /*
     * 申请售后
     */
    public function applyAfterSale() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $order = OrderModel::with(['orderGoods'])
            ->where([
                ['user_id', '=', $user->id],
                ['id', '=', input('post.order_id', 0)],
            ])->findOrEmpty();
        if($order->isEmpty()) throw new BadRequestException(['errorMessage' => '订单不存在']);
        if(!in_array($order->status, [2, 3])) throw new BadRequestException(['errorMessage' => '订单不是待发货或待收货状态']);
        $aftermarket = AftermarketModel::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'order_status' => $order->status,
            'aftermarket_no' => time() . mt_rand(100000, 999999),
            'refund_quantity' => count($order->orderGoods),
            'refund_amount' => $order->amount,
            'goods_status' => input('post.goods_status', ''),
            'refund_reason' => input('post.refund_reason', ''),
            'refund_remark' => input('post.refund_remark', ''),
        ]);
        if($aftermarket->isEmpty()) throw new BadRequestException(['errorMessage' => '失败']);
        $order->status = 7;
        if(!$order->save()) throw new BadRequestException(['errorMessage' => '失败']);
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改售后
     */
    public function editAftermarket() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $aftermarket = AftermarketModel::where([
            ['user_id', '=', $user->id],
            ['id', '=', input('post.id', 0)],
        ])->findOrEmpty();
        if($aftermarket->isEmpty()) throw new BadRequestException(['errorMessage' => '售后不存在']);
        if($aftermarket->status == 1) throw new BadRequestException(['errorMessage' => '售后已通过审核']);
        $aftermarket->goods_status = input('post.goods_status', '');
        $aftermarket->refund_reason = input('post.refund_reason', '');
        $aftermarket->refund_remark = input('post.refund_remark', '');
        $aftermarket->status = 0;
        $aftermarket->audit_time = 0;
        $aftermarket->reject_reason = '';
        if(!$aftermarket->save()) throw new BadRequestException(['errorMessage' => '失败']);
        return returnResponse(200, '成功', []);
    }

    /*
     * 删除售后
     */
    public function deleteAftermarket() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $ids = explode(',', input('post.id', ''));
        foreach ($ids as $id){
            $aftermarket = AftermarketModel::where([
                ['user_id', '=', $user->id],
                ['id', '=', $id],
            ])->findOrEmpty();
            if($aftermarket->isEmpty()) throw new BadRequestException(['errorMessage' => '售后不存在']);
            if($aftermarket->status == 0) throw new BadRequestException(['errorMessage' => '正在售后中']);
            if($aftermarket->status == 1 && $aftermarket->refund_time == 0) throw new BadRequestException(['errorMessage' => '正在退款中']);
            if(!$aftermarket->delete()) throw new BadRequestException(['errorMessage' => '售后删除失败']);
        }
        return returnResponse(200, '成功', []);
    }

    /*
     * 取消售后
     * 删除售后并恢复订单状态
     */
    public function cancelAftermarket() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $aftermarket = AftermarketModel::where([
            ['user_id', '=', $user->id],
            ['id', '=', input('post.id', 0)],
        ])->findOrEmpty();
        if($aftermarket->isEmpty()) throw new BadRequestException(['errorMessage' => '售后不存在']);
        if($aftermarket->status == 1) throw new BadRequestException(['errorMessage' => '售后已通过审核']);
        if(!$aftermarket->delete()) throw new BadRequestException(['errorMessage' => '失败']);

        $order = OrderModel::where([
            ['user_id', '=', $user->id],
            ['id', '=', $aftermarket->order_id],
        ])->findOrEmpty();
        if(!$order->isEmpty()) {
            $order->status = $aftermarket->order_status;
            $order->save();
        }

        return returnResponse(200, '成功', []);
    }

    /*
     * 自动取消售后
     * 商家驳回售后1天后若用户没有修改重新提交售后则自动取消售后
     */
    public function automaticCancelAftermarket()
    {
        $aftermarketList = AftermarketModel::with(['order'])
            ->where([
                ['status', '=', 2],
                ['audit_time', '<', (time() - 1*24*60*60)],
            ])->select();

        $data = [];
        $id = [];

        foreach ($aftermarketList as $aftermarket){
            //更改订单状态
            if ($aftermarket->order){
                array_push($data, ['id' => $aftermarket->order->id, 'status' => $aftermarket->order_status]);
            }
            array_push($id, $aftermarket->id);
        }

        //批量删除
        AftermarketModel::where('id', 'in', $id)->delete();
        //批量更新
        (new OrderModel())->saveAll($data);

        return returnResponse(200, '成功', []);
    }

    /*
     * 自动确认收货
     * 商家发货七天后若用户没有收货则自动确认收货
     */
    public function automaticConfirmReceipt() :Json
    {
        $time = time();

        $orderList = OrderModel::with([
            'user'
        ])->where([
            ['status', '=', 3],
            ['delivery_time', '<', ($time - 7*24*60*60)],
        ])->select();

        $data = [];

        foreach ($orderList as $order){
            //更改订单状态
            array_push($data, ['id' => $order->id, 'receipt_time' => $time, 'status' => 4]);
        }

        //批量更新
        (new OrderModel())->saveAll($data);

        return returnResponse(200, '成功', []);
    }

    /*
     * 获取评价列表
     */
    public function getEvaluateList() :Json
    {
        list($page, $limit) = get_page_limit();
        $user_id = input('get.user_id', '');
        $goods_id = input('get.goods_id', '');
        $attribute = input('get.attribute', '');
        $goods_specification_id = input('get.goods_specification_id', '');

        $condition = [];

        if(!empty($user_id)){
            array_push($condition, ['user_id','=',$user_id]);
        }
        if(!empty($goods_id)){
            array_push($condition, ['goods_id','=',$goods_id]);
        }
        if(!empty($attribute)){
            array_push($condition, ['attribute','=',$attribute]);
        }
        if(!empty($goods_specification_id)){
            array_push($condition, ['goods_specification_id','=',$goods_specification_id]);
        }

        $list = EvaluateModel::with([
            'user',
            'goods',
            'goodsSpecification',
        ])->where($condition)
            ->limit($limit)
            ->page($page)
            ->order(['create_time' => 'desc'])
            ->select();
        $total = EvaluateModel::where($condition)->count();

        return returnResponse(200, '成功', [
            'list' => $list,
            'total' => $total,
        ]);
    }

    /*
     * 添加评价
     */
    public function addEvaluate() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $order = OrderModel::with(['orderGoods'])
            ->where([
                ['user_id', '=', $user->id],
                ['id', '=', input('post.order_id', 0)],
            ])->findOrEmpty();
        if($order->isEmpty()) throw new BadRequestException(['errorMessage' => '订单不存在']);
        if($order->status != 4) throw new BadRequestException(['errorMessage' => '订单不是待评价状态']);
        if(!$order->orderGoods) throw new BadRequestException(['errorMessage' => '订单中没有商品存在']);

        $evaluate = input('post.evaluate', ''); //[{"order_goods_id":订单商品id,"score":分数,"image":图片,"content":内容}]
        $evaluate = $evaluate ? json_decode($evaluate, true) : [];

        $data = [];
        foreach ($evaluate as $item1) {
            foreach ($order->orderGoods as $item2) {
                if($item1['order_goods_id'] == $item2->id){
                    array_push($data, [
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'order_goods_id' => $item2->id,
                        'goods_id' => $item2->goods_id,
                        'quantity' => $item2->quantity,
                        'attribute' => $item2->attribute,
                        'goods_specification_id' => $item2->goods_specification_id,
                        'score' => $item1['score'],
                        'image' => $item1['image'],
                        'content' => $item1['content'],
                    ]);
                }
            }
        }
        if(!count($data)) throw new BadRequestException(['errorMessage' => '订单商品不存在']);

        //批量更新
        $res = (new EvaluateModel())->saveAll($data);
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);

        //更新订单状态
        $order->status = 5;
        if(!$order->save()) throw new BadRequestException(['errorMessage' => '失败']);

        return returnResponse(200, '成功', []);
    }

    /*
    * 删除评价
    */
    public function deleteEvaluate() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $id = input('post.id', '');
        $res = EvaluateModel::where([
            ['user_id', '=', $user->id],
            ['id', 'in', explode(',', $id)],
        ])->delete();
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取售后列表
     */
    public function getAftermarketList() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        list($page, $limit) = get_page_limit();
        $aftermarket_no = input('get.aftermarket_no', '');
        $status = input('get.status', '');

        $condition = [
            ['user_id', '=', $user->id],
        ];
        if(!empty($aftermarket_no)){
            array_push($condition, ['aftermarket_no','=',$aftermarket_no]);
        }
        if($status != ''){
            array_push($condition, ['status','=',$status]);
        }

        $list = AftermarketModel::with([
            'order' => [
                'orderGoods' => [
                    'goodsSpecification',
                    'goods' => [
                        'goodsSpecification'
                    ]
                ]
            ]
        ])->where($condition)
            ->limit($limit)
            ->page($page)
            ->order(['create_time' => 'desc'])
            ->select();
        $total = AftermarketModel::where($condition)->count();

        return returnResponse(200, '成功', [
            'list' => $list,
            'total' => $total,
        ]);
    }

    /*
     * 获取售后信息
     */
    public function getAftermarketInfo() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $aftermarket = AftermarketModel::with([
            'order' => [
                'orderGoods' => [
                    'goodsSpecification',
                    'goods' => [
                        'goodsSpecification'
                    ]
                ]
            ]
        ])->where([
            ['user_id', '=', $user->id],
            ['id', '=', input('get.id', 0)],
        ])->findOrEmpty();

        return returnResponse(200, '成功', $aftermarket);
    }

    /*
     * 更换密码
     */
    public function changePassword() :Json
    {
        $mobile = input('post.mobile', '');
        $captcha = input('post.captcha', '');
        $password = input('post.password', '');

        if(empty($mobile)) throw new BadRequestException(['errorMessage' => '手机号不能为空']);
        if(empty($password) || strlen($password) < 5) throw new BadRequestException(['errorMessage' => '密码长度不能小于5']);

        $user = UserModel::where('mobile', '=', $mobile)->findOrEmpty();
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户手机号不存在']);

        //验证验证码
        $captchaInfo = CaptchaModel::where([
            ['mobile', '=', $mobile],
            ['expire_time', '>', time()],
        ])->order(['id' => 'desc'])
            ->findOrEmpty();
        if($captchaInfo->isEmpty()) throw new BadRequestException(['errorMessage' => '验证码不存在或已过期']);
        if($captcha != $captchaInfo->captcha) throw new BadRequestException(['errorMessage' => '验证码不正确']);

        $user->password = $password;
        $res = $user->save();

        if($res){
            //更新验证码
            $captchaInfo->expire_time = time();
            $captchaInfo->save();
            return returnResponse(200, '成功', []);
        }else{
            throw new BadRequestException(['errorMessage' => '失败']);
        }
    }


    /*
     * 获取热销商品，显示热门商品
     */
    public function getHotGoods() :Json
    {
        $goods = GoodsModel::with(['goodsSpecification'])
            ->where([
                ['is_recommend_hot', '=', 1],
                ['status', '=', 1],
            ])->order('create_time', 'desc')
            ->limit(4)
            ->select();

        return returnResponse(200, '成功', $goods);
    }

    /*
     * 获取用户中心的统计数据
     */
    public function getStatisticsData() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        //订单总金额
        $orderTotalAmount = OrderModel::where([
            ['user_id', '=', $user->id],
            ['status', 'in', [1,2,3,4,5]],
        ])->sum('amount');

        //运输中订单数量
        $inTransitOrderCount = OrderModel::where([
            ['user_id', '=', $user->id],
            ['status', '=', 3],
        ])->count();

        //系统消息数量
        $systemMessagesCount = NoticeModel::count();

        //最近一周的下单数量
        $recentlyOrderCount = OrderModel::where([
            ['user_id', '=', $user->id],
            ['status', 'in', [1,2,3,4,5]],
            ['create_time', '>', (time() - 7*24*60*60)],
        ])->count();

        return returnResponse(200, '成功', [
            'orderTotalAmount' => $orderTotalAmount,
            'inTransitOrderCount' => $inTransitOrderCount,
            'systemMessagesCount' => $systemMessagesCount,
            'recentlyOrderCount' => $recentlyOrderCount,
        ]);
    }

    /*
     * 获取微信openid通过code
     */
    public function getWeChatOpenIdByCode() :Json
    {
        $code = input('get.code', '');
        if(empty($code)) throw new BadRequestException(['errorMessage' => '微信临时登录凭证不能为空']);
        $jsSdk = new WeChatJsSdkService();
        $openId = $jsSdk->code2Session($code);
        return returnResponse(200, '成功', ['openid' => $openId]);
    }

    /*
     * 获取微信手机号码通过code
     */
    public function getWeChatPhoneNumberByCode() :Json
    {
        $code = input('get.code', '');
        $openid = input('get.openid', '');
        if(empty($code)) throw new BadRequestException(['errorMessage' => '微信临时登录凭证不能为空']);
        if(empty($openid)) throw new BadRequestException(['errorMessage' => '微信用户唯一标识不能为空']);
        $jsSdk = new WeChatJsSdkService();
        $phoneInfo = $jsSdk->getPhoneNumber($code);
        //用户微信绑定的手机号
        $mobile = $phoneInfo['phoneNumber'];
        //判断用户是否存在
        $user = UserModel::where('mobile', '=', $mobile)->findOrEmpty();
        if(!$user->isEmpty() && $user->openid == ''){
            $user->openid = $openid;
            $user->save();
        }
        return returnResponse(200, '成功', [
            'mobile' => $mobile,
            'is_register' => !$user->isEmpty(), //是否已经注册
        ]);
    }

    /*
     * 注册通过微信
     * https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/getPhoneNumber.html
     */
    public function registerByWeChat() :Json
    {
        $mobile = input('post.mobile', '');
        $openid = input('post.openid', '');
        $nickname = input('post.nickname', '');
        $avatar = input('post.avatar', '');

        if(empty($mobile)) throw new BadRequestException(['errorMessage' => '手机号不能为空']);
        if(empty($openid)) throw new BadRequestException(['errorMessage' => '微信用户唯一标识不能为空']);
        $user = UserModel::where('mobile', '=', $mobile)->findOrEmpty();
        if(!$user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户手机号已存在']);

        //注册
        $user = UserModel::create([
            'mobile'  =>  $mobile,
            'openid'  =>  $openid,
            'nickname'  =>  $nickname,
            'avatar'  =>  $avatar,
            'level'  =>  1,
            'is_use'  =>  1,
        ]);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '失败']);

        //返回token
        $token = UserService::getUserTokenById((int)$user->id);
        return returnResponse(200, '成功', ['token' => $token]);
    }

    /*
     * 分享通过微信
     */
    public function shareByWeChat() :Json
    {
        $jsSdk = new WeChatJsSdkService();
        $signPackage = $jsSdk->GetSignPackage();
        return returnResponse(200, '成功', $signPackage);
    }







}
