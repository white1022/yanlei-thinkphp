<?php
declare (strict_types = 1);

namespace app\index\controller;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Goods as GoodsModel;
use app\common\model\Order as OrderModel;
use app\common\model\OrderGoods as OrderGoodsModel;
use app\common\model\Transaction as TransactionModel;
use app\common\service\AliPay as AliPayService;
use app\common\service\Redis as RedisService;
use think\facade\Log;

class AliPay extends Base
{
    /*
     * 异步通知
     * 异步通知参数 https://opendocs.alipay.com/open/204/105301#%E5%BC%82%E6%AD%A5%E8%BF%94%E5%9B%9E%E7%BB%93%E6%9E%9C%E7%9A%84%E9%AA%8C%E7%AD%BE
     */
    public function callback()
    {
        $data = input('post.', []);
        //记录日志
        Log::write('支付宝支付异步回调通知数据：' . json_encode($data));
        $flag = AliPayService::checkSignature($data);
        if($flag && $data['trade_status'] == 'TRADE_SUCCESS'){
            //当前时间
            $time = time();
            //查询订单数据
            $order = OrderModel::where([
                ['order_no', '=', $data['out_trade_no']],
                ['amount', '=', $data['total_amount']],
                ['status', '=', 1],
            ])->findOrEmpty();
            if(!$order->isEmpty()){
                //更新订单状态并写入支付宝的交易号
                $order->payment = 2;
                $order->payment_time = $time;
                $order->status = 2;
                $order->out_order_no = $data['trade_no'];
                if($order->save()){
                    //判断商品库存
                    $orderGoods = OrderGoodsModel::with(['goods'])
                        ->where('order_no', '=', $order->order_no)
                        ->group('goods_id')
                        ->field(['goods_id', 'sum(quantity)' => 'quantity'])
                        ->select();
                    $goods = [];
                    foreach ($orderGoods as $item){
                        if($item->goods){
                            //商品减库存，加销量
                            array_push($goods, [
                                'id' => $item->goods->id,
                                'stock' => $item->goods->stock < $item->quantity ? 0 : pricecalc($item->goods->stock, '-', $item->quantity),
                                'sales' => pricecalc($item->goods->sales, '+', $item->quantity),
                            ]);
                        }

                    }
                    //批量更新商品
                    (new GoodsModel())->saveAll($goods);

                    //添加交易记录
                    TransactionModel::create([
                        'user_id' => $order->user_id,
                        'type' => 1,
                        'amount' => $order->amount,
                    ]);

                    //订单状态写入缓存
                    $redis = RedisService::getInstance();
                    $redis->setex('ORDERNO:'.$order->order_no, 7200, $order->status);

                    exit('success');
                }
            }
        }
        exit('fail');
    }

    /*
     * 支付
     */
    public function pay()
    {
        $scene = input('post.scene', '');
        $order_no = input('post.order_no', '');

        if(empty($scene)) throw new BadRequestException(['errorMessage' => '场景不能为空']);

        //查询订单
        $order = OrderModel::where([
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
        foreach ($orderGoods as $item){
            if(empty($item->goods)) throw new BadRequestException(['errorMessage' => '商品不存在']);
            if($item->goods->stock < $item->quantity) throw new BadRequestException(['errorMessage' => '商品['.$item->goods->name.']库存不足']);
        }

        //组装业务数据
        $data = [
            'total_amount' => $order->amount, //订单总金额，单位是元
            'subject' => $order->title, //订单标题
            'out_trade_no' => $order->order_no, //商户网站唯一订单号
        ];

        // 调起支付
        if($scene == 1){
            $result = AliPayService::appPay($data);
        }elseif ($scene == 2){
            $data['return_url'] = 'https://index.xiaozhannl.com/#/member/myorder?type=2'; //支付成功后页面的跳转地址
            $result = AliPayService::pcPay($data);
            //订单状态写入缓存
            $redis = RedisService::getInstance();
            $redis->setex('ORDERNO:'.$order->order_no, 7200, $order->status);
        }else{
            throw new BadRequestException(['errorMessage' => '场景不存在']);
        }


        return returnResponse(200, '成功', $result);
    }



}