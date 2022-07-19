<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Aftermarket as AftermarketModel;
use app\common\model\Order as OrderModel;
use app\common\model\Transaction as TransactionModel;
use app\common\model\User as UserModel;
use app\common\service\AliPay as AliPayService;
use app\common\service\WeChatPay as WeChatPayService;
use think\facade\Log;


class Aftermarket
{
    /*
     * 修改售后信息
     */
    public static function editAftermarketInfo() :void
    {
        $data = input('post.');
        $data['audit_time'] = time();
        $data['refund_path'] = '资金将按支付路径原路退还';

        $aftermarket = AftermarketModel::with(['order'])
            ->findOrEmpty(input('post.id'));
        if($aftermarket->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        if($aftermarket->status != 0) throw new BadRequestException(['errorMessage' => '售后不是待处理状态']);
        if(empty($aftermarket->order)) throw new BadRequestException(['errorMessage' => '订单不存在']);
        if($aftermarket->order->status != 7) throw new BadRequestException(['errorMessage' => '订单不是申请售后状态']);
        if(!$aftermarket->save($data)) throw new BadRequestException(['errorMessage' => '失败']);

        //调起退款
        if ($aftermarket->status == 1){
            $user = UserModel::findOrEmpty($aftermarket->order->user_id);
            if(!$user->isEmpty()){
                if ($aftermarket->order->payment == 1){
                    //余额
                    $user->balance = pricecalc($user->balance, '+', $aftermarket->order->amount);
                    $user->save();
                }elseif ($aftermarket->order->payment == 2){
                    //支付宝
                    $data = [
                        'out_trade_no' => $aftermarket->order->order_no, //商户网站唯一订单号
                        'refund_amount' => $aftermarket->order->amount, //退款金额，单位是元
                    ];
                    $result = AliPayService::refund($data);
                    //记录日志
                    Log::write('支付宝退款返回数据：' . json_encode($result));
                }elseif ($aftermarket->order->payment == 3){
                    //微信
                    $data = [
                        'transaction_id' => $aftermarket->order->out_order_no, //微信支付订单号
                        'out_refund_no' => $aftermarket->aftermarket_no, //商户退款单号
                        'refund' => $aftermarket->order->amount * 100, //退款金额，单位是分
                        'total' => $aftermarket->order->amount * 100, //订单金额，单位是分
                    ];
                    $result = WeChatPayService::refund($data);
                    //记录日志
                    Log::write('微信退款返回数据：' . json_encode($result));
                }else{
                    throw new BadRequestException(['errorMessage' => '订单支付方式不存在']);
                }

                //更新售后
                $aftermarket->refund_time = time();
                $aftermarket->save();

                //添加交易记录
                TransactionModel::create([
                    'user_id' => $aftermarket->order->user_id,
                    'type' => 3,
                    'amount' => $aftermarket->order->amount,
                ]);
            }
        }

    }
}