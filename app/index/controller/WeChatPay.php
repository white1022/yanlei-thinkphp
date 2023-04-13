<?php
declare (strict_types = 1);

namespace app\index\controller;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Goods as GoodsModel;
use app\common\model\Order as OrderModel;
use app\common\model\OrderGoods as OrderGoodsModel;
use app\common\model\Transaction as TransactionModel;
use app\common\service\Redis as RedisService;
use app\common\service\WeChatPay as WeChatPayService;
use app\common\service\Qrcode as QrcodeService;
use think\facade\Log;
use think\facade\Request;

class WeChatPay extends Base
{
    /*
     * 异步回调通知
     * 具体参考 https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_2_5.shtml
     */
    public function callback()
    {
        $data = [
            'inWechatpaySignature' => Request::header('Wechatpay-Signature'),
            'inWechatpayTimestamp' => Request::header('Wechatpay-Timestamp'),
            'inWechatpaySerial' => Request::header('Wechatpay-Serial'),
            'inWechatpayNonce' => Request::header('Wechatpay-Nonce'),
            'inBody' => file_get_contents('php://input'),
        ];
        //验签并返回解密后的数据
        $result = WeChatPayService::checkSignature($data);
        //记录日志
        Log::write('微信支付异步回调通知数据：' . json_encode($result));
        //判断支付成功通知参数是否正确
        if($result['trade_state'] == 'SUCCESS'){
            //当前时间
            $time = time();
            //查询订单数据
            $result['out_trade_no'] = explode('_', $result['out_trade_no'])[1]; //过滤掉添加的前缀
            $order = OrderModel::where([
                ['order_no', '=', $result['out_trade_no']],
                ['amount', '=', $result['amount']['total'] / 100],
                ['status', '=', 1],
            ])->findOrEmpty();
            if(!$order->isEmpty()){
                //更新订单状态并写入微信的交易号
                $order->payment = 3;
                $order->payment_time = $time;
                $order->status = 2;
                $order->out_order_no = $result['transaction_id'];
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

                    exit('{"code": "SUCCESS","message": "成功"}');
                }
            }
        }
        exit('{"code": "FAIL","message": "失败"}');
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
        $order = OrderModel::with(['user'])
            ->where([
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
            'total' => $order->amount * 100, //订单总金额，单位是分(1元=100分)
            'description' => $order->title, //商品描述
            'out_trade_no' => $order->order_no, //商户订单号
        ];

        // 调起支付
        // 由于微信不支持跨场景支付，所以需要给订单号加个前缀
        if($scene == 1){
            $data['out_trade_no'] = 'APP_' . $data['out_trade_no'];
            $result = WeChatPayService::appPay($data);
        }elseif ($scene == 2){
            $data['out_trade_no'] = 'NATIVE_' . $data['out_trade_no'];
            $result = WeChatPayService::pcPay($data);
            //订单状态写入缓存
            $redis = RedisService::getInstance();
            $redis->setex('ORDERNO:'.$order->order_no, 7200, $order->status);
        }elseif ($scene == 3){
            $data['out_trade_no'] = 'JSAPI_' . $data['out_trade_no'];
            $data['openid'] = $order->user->openid;
            $result = WeChatPayService::jsapiPay($data);
        }elseif ($scene == 4){
            $data['out_trade_no'] = 'JSAPI_' . $data['out_trade_no'];
            $data['openid'] = $order->user->openid;
            $result = WeChatPayService::miniPay($data);
        }else{
            throw new BadRequestException(['errorMessage' => '场景不存在']);
        }

        return returnResponse(200, '成功', $result);
    }

    /*
     * 更新平台证书
     * 注意：第一次需要手动下载，然后使用接口定期更新证书
     * https://github.com/wechatpay-apiv3/wechatpay-php/blob/main/bin/README.md
     */
    public function updatePlatformCertificate()
    {
        $result = WeChatPayService::downloadPlatformCertificate();
        //使用最新的证书
        $platformCertificate = WeChatPayService::decrypt($result['data'][0]['encrypt_certificate']);
        //把一个字符串写入文件中
        $bool = file_put_contents(config('config.wechatpay.platformCertificate'), $platformCertificate);
        if ($bool == false) throw new BadRequestException(['errorMessage' => '失败']);
        return returnResponse(200, '成功', []);
    }



}