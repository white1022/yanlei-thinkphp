<?php
declare (strict_types = 1);

namespace app\index\controller;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Address as AddressModel;
use app\common\model\Goods as GoodsModel;
use app\common\model\Order as OrderModel;
use app\common\model\OrderGoods as OrderGoodsModel;
use app\common\model\Evaluate as EvaluateModel;
use app\common\model\SystemSetup as SystemSetupModel;
use app\common\model\Transaction as TransactionModel;
use app\common\model\User as UserModel;
use app\common\service\Redis as RedisService;
use think\model\Relation;
use think\response\Json;

class Order extends Base
{
    /*
     * 创建订单
     */
    public function createOrder() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $address = AddressModel::where([
            ['user_id', '=', $user->id],
            ['id', '=', input('post.address_id', 0)],
        ])->findOrEmpty();
        if($address->isEmpty()) throw new BadRequestException(['errorMessage' => '地址不存在']);

        $goods = input('post.goods', ''); //[{"goods_id":商品id,"quantity":商品数量,"attribute":商品属性,"goods_specification_id":商品规格表id}]
        $goods = $goods ? json_decode($goods, true) : [];

        //判断商品库存并计算价格
        $order_goods = [];
        $amount = '0.00';
        $freight = '0.00';
        foreach ($goods as $k => $v) {
            $goodsInfo = GoodsModel::with(['goodsSpecification' => function(Relation $query) use ($v) {
                $query->where('id', '=', $v['goods_specification_id']);
            }])->findOrEmpty($v['goods_id']);
            if($goodsInfo->isEmpty()) throw new BadRequestException(['errorMessage' => "商品不存在"]);
            if((int)$v['quantity'] <= 0) throw new BadRequestException(['errorMessage' => "数量参数必须是正整数"]);
            if($goodsInfo->stock < (int)$v['quantity']) throw new BadRequestException(['errorMessage' => "商品[$goodsInfo->name]库存不足"]);
            if(!in_array($v['attribute'], explode(',', $goodsInfo->attribute))) throw new BadRequestException(['errorMessage' => "商品属性不存在"]);
            if(!count($goodsInfo->goodsSpecification)) throw new BadRequestException(['errorMessage' => "商品规格不存在"]);
            $price = pricecalc($goodsInfo->goodsSpecification[0]->price, '*', $v['quantity']);
            $amount = pricecalc($amount, '+', $price);
            $freight = (bccomp($freight, $goodsInfo->freight, 2) >= 0) ? $freight : $goodsInfo->freight;
            array_push($order_goods, [
                'goods_id' => $goodsInfo->id,
                'quantity' => (int)$v['quantity'],
                'attribute' => $v['attribute'],
                'goods_specification_id' => $goodsInfo->goodsSpecification[0]->id,
                'snapshot' => json_encode($goodsInfo->toArray()),
            ]);
        }
        $amount = pricecalc($amount, '+', $freight);
        if(!count($order_goods)) throw new BadRequestException(['errorMessage' => '订单商品不能为空']);

        //创建订单
        $order = OrderModel::create([
            'title' => '轻食商品',
            'user_id' => $user->id,
            'address_id' => $address->id,
            'order_no' => time() . mt_rand(100000, 999999),
            'amount' => $amount,
            'freight' => $freight,
            'remark' => input('post.remark', ''),
            'is_remind_delivery' => 2,
            'status' => 1,
        ]);
        if($order->isEmpty()) throw new BadRequestException(['errorMessage' => '创建订单失败']);

        //写入订单商品
        foreach ($order_goods as &$item) {
            $item['order_no'] = $order->order_no;
        }
        $res = (new OrderGoodsModel)->saveAll($order_goods);

        return returnResponse(200, '成功', [
            'order_no' => $order->order_no,
        ]);
    }

    /*
     * 去支付
     * 默认使用余额支付
     */
    public function toPay() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $order_no = input('post.order_no', '');

        //查询订单
        $order = OrderModel::where([
            ['user_id', '=', $user->id],
            ['order_no', '=', $order_no],
        ])->findOrEmpty();
        if($order->isEmpty()) throw new BadRequestException(['errorMessage' => '订单不存在']);
        if($order->status != 1) throw new BadRequestException(['errorMessage' => '订单不是待付款状态']);

        //判断商品库存
        $orderGoods = OrderGoodsModel::with(['goods'])
            ->where('order_no', '=', $order->order_no)
            ->group('goods_id')
            ->field(['goods_id', 'sum(quantity)' => 'quantity'])
            ->select();
        $goods = [];
        foreach ($orderGoods as $item){
            if(empty($item->goods)) throw new BadRequestException(['errorMessage' => '商品不存在']);
            if($item->goods->stock < $item->quantity) throw new BadRequestException(['errorMessage' => '商品['.$item->goods->name.']库存不足']);
            //商品减库存，加销量
            array_push($goods, [
                'id' => $item->goods->id,
                'stock' => pricecalc($item->goods->stock, '-', $item->quantity),
                'sales' => pricecalc($item->goods->sales, '+', $item->quantity),
            ]);
        }

        if($user->balance < $order->amount) throw new BadRequestException(['errorMessage' => '用户余额不足']);
        $user->balance = pricecalc($user->balance, '-', $order->amount);
        if(!$user->save()) throw new BadRequestException(['errorMessage' => '余额支付失败']);
        $order->payment = 1;
        $order->payment_time = time();
        $order->status = 2;
        if(!$order->save()) throw new BadRequestException(['errorMessage' => '订单状态保存失败']);

        //批量更新商品
        (new GoodsModel())->saveAll($goods);

        //添加交易记录
        $transaction = TransactionModel::create([
            'user_id' => $user->id,
            'type' => 1,
            'amount' => $order->amount,
        ]);
        if($transaction->isEmpty()) throw new BadRequestException(['errorMessage' => '交易记录添加失败']);
        return returnResponse(200, '成功', []);
    }

    /*
    * 获取订单信息
    */
    public function getOrderInfo() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $order_no = input('get.order_no', '');
        $order = OrderModel::with([
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
        ])->where([
            ['user_id', '=', $user->id],
            ['order_no', '=', $order_no],
        ])->findOrEmpty();
        if($order->isEmpty()) throw new BadRequestException(['errorMessage' => '订单不存在']);

        return returnResponse(200, '成功', $order);
    }

    /*
     * 获取订单评价
     */
    public function getOrderEvaluate() :Json
    {
        $order_id = input('get.order_id', '');
        $evaluate = EvaluateModel::with([
            'orderGoods' => [
                'goods'
            ]
        ])->where([
            ['order_id', '=', $order_id],
        ])->select();
        return returnResponse(200, '成功', $evaluate);
    }

    /*
    * 删除订单
    */
    public function deleteOrder() :Json
    {
        $user = UserModel::findOrEmpty($this->userId);
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在']);

        $ids = explode(',', input('post.id', ''));
        foreach ($ids as $id){
            $order = OrderModel::where([
                ['user_id', '=', $user->id],
                ['id', '=', $id],
            ])->findOrEmpty();
            if($order->isEmpty()) throw new BadRequestException(['errorMessage' => '订单不存在']);
            if(!in_array($order->status, [1,6])) throw new BadRequestException(['errorMessage' => '订单不是待付款或已取消状态']);
            if(!$order->delete()) throw new BadRequestException(['errorMessage' => '订单删除失败']);
            //删除订单商品
            OrderGoodsModel::where('order_no', '=', $order->order_no)->delete();
        }
        return returnResponse(200, '成功', []);
    }

    /*
    * 获取订单状态通过缓存
     * 用于在PC端网站的支付页面实时判断订单支付状态并跳转对应的订单列表页面
    */
    public function getOrderStatusByRedis() :Json
    {
        $order_no = input('get.order_no', '');
        if(empty($order_no)) throw new BadRequestException(['errorMessage' => '订单编号不能为空']);

        //读取换成
        $redis = RedisService::getInstance();
        $status = $redis->get('ORDERNO:'.$order_no);
        if(!$status) throw new BadRequestException(['errorMessage' => '订单状态缓存不存在或已过期']);

        return returnResponse(200, '成功', [
            'status' => $status,
        ]);
    }

}