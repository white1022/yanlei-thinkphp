<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\service\Qrcode as QrcodeService;
use think\Exception;
use think\facade\Request;
use WeChatPay\Builder;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Util\PemUtil;
use WeChatPay\Formatter;
use WeChatPay\Crypto\AesGcm;

/*
 * https://github.com/wechatpay-apiv3/wechatpay-php
 */
class WeChatPay
{
    /*
     * 设置参数
     */
    private static function getOptions()
    {
        // 商户号
        $merchantId = config('config.wechatpay.merchantId');

        // 从本地文件中加载「商户API私钥」，「商户API私钥」会用来生成请求的签名
        $merchantPrivateKeyFilePath = 'file://' . config('config.wechatpay.merchantPrivateKey');
        $merchantPrivateKeyInstance = Rsa::from($merchantPrivateKeyFilePath, Rsa::KEY_TYPE_PRIVATE);

        // 「商户API证书」的「证书序列号」
        $merchantCertificateSerial = config('config.wechatpay.merchantCertificateSerial');

        // 从本地文件中加载「微信支付平台证书」，用来验证微信支付应答的签名
        $platformCertificateFilePath = 'file://' . config('config.wechatpay.platformCertificate');
        $platformPublicKeyInstance = Rsa::from($platformCertificateFilePath, Rsa::KEY_TYPE_PUBLIC);

        // 从「微信支付平台证书」中获取「证书序列号」
        $platformCertificateSerial = PemUtil::parseCertificateSerialNo($platformCertificateFilePath);

        // 构造一个 APIv3 客户端实例
        $instance = Builder::factory([
            'mchid'      => $merchantId,
            'serial'     => $merchantCertificateSerial,
            'privateKey' => $merchantPrivateKeyInstance,
            'certs'      => [
                $platformCertificateSerial => $platformPublicKeyInstance,
            ],
        ]);

        return $instance;
    }

    /*
     * 生成签名
     * https://pay.weixin.qq.com/wiki/doc/apiv3/wechatpay/wechatpay4_0.shtml
     */
    private static function generateSignature($params = [])
    {
        try {
            //$merchantPrivateKeyFilePath = 'file://' . config('config.wechatpay.merchantPrivateKey');
            //$merchantPrivateKeyInstance = Rsa::from('file://' . config('config.wechatpay.merchantPrivateKey'));

            $params += [
                'paySign' => Rsa::sign(Formatter::joinedByLineFeed(...array_values($params)), Rsa::from('file://' . config('config.wechatpay.merchantPrivateKey'))), //签名
                'signType' => 'RSA' //签名方式
            ];

            return json_encode($params);
        } catch (Exception $e) {
            throw new BadRequestException(['errorMessage' => $e->getMessage()]);
        }
    }

    /*
     * 下载微信支付的平台证书
     * https://pay.weixin.qq.com/wiki/doc/apiv3/wechatpay/wechatpay3_1.shtml
     */
    public static function downloadPlatformCertificate()
    {
        //1. 设置参数
        $instance = self::getOptions();

        try {
            //2. 发送请求
            $resp = $instance
                ->chain('v3/certificates')
                ->get([
                    'debug' => true // 调试模式，https://docs.guzzlephp.org/en/stable/request-options.html#debug
                ]);
            //3. 处理响应或异常
            if ($resp->getStatusCode() != 200) throw new BadRequestException(['errorMessage' => $resp->getReasonPhrase()]);
            $result = $resp->getBody();
            return json_decode((string)$result, true);
        } catch (Exception $e) {
            throw new BadRequestException(['errorMessage' => $e->getMessage()]);
        }
    }

    /*
     * 退款
     * https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_2_9.shtml
     */
    public static function refund($data = [])
    {
        //1. 设置参数
        $instance = self::getOptions();

        try {
            //2. 发送请求
            $resp = $instance
                ->chain('v3/refund/domestic/refunds')
                ->post([
                    'json' => [
                        'transaction_id' => $data['transaction_id'], //微信支付订单号
                        'out_refund_no'  => $data['out_refund_no'], //商户退款单号
                        'amount'         => [
                            'refund'   => $data['refund'], //退款金额
                            'total'    => $data['total'], //原订单金额
                            'currency' => 'CNY', //退款币种
                        ],
                    ],
                ]);
            //3. 处理响应或异常
            if ($resp->getStatusCode() != 200) throw new BadRequestException(['errorMessage' => $resp->getReasonPhrase()]);
            $result = $resp->getBody();
            return json_decode((string)$result, true);
        } catch (Exception $e) {
            throw new BadRequestException(['errorMessage' => $e->getMessage()]);
        }
    }

    /*
     * PC支付
     * https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_4_1.shtml
     */
    public static function pcPay($data = [])
    {
        //1. 设置参数
        $instance = self::getOptions();

        try {
            //2. 发送请求
            $resp = $instance
                ->chain('v3/pay/transactions/native')
                ->post([
                    'json' => [
                        'mchid'        => config('config.wechatpay.merchantId'), //直连商户号
                        'out_trade_no' => $data['out_trade_no'], //商户订单号
                        'appid'        => config('config.wechatpay.appId'), //应用ID
                        'description'  => $data['description'], //商品描述
                        'notify_url'   => config('config.wechatpay.notifyUrl'), //通知地址
                        'amount'       => [
                            'total'    => $data['total'], //总金额
                            'currency' => 'CNY' //货币类型
                        ],
                    ]
                ]);
            //3. 处理响应或异常
            if ($resp->getStatusCode() != 200) throw new BadRequestException(['errorMessage' => $resp->getReasonPhrase()]);
            $result = $resp->getBody();
            $array = json_decode((string)$result, true);
            //Native调起支付 https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_4_4.shtml
            return [
                'qrcode' => QrcodeService::getInstance([
                    'data' => $array['code_url'],
                    'labelText'=>'微信支付',
                ])->dataUri(), //生成二维码
            ];
        } catch (Exception $e) {
            throw new BadRequestException(['errorMessage' => $e->getMessage()]);
        }
    }

    /*
     * APP支付
     * https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_2_1.shtml
     */
    public static function appPay($data = [])
    {
        //1. 设置参数
        $instance = self::getOptions();

        try {
            //2. 发送请求
            $resp = $instance
                ->chain('v3/pay/transactions/app')
                ->post([
                    'json' => [
                        'mchid'        => config('config.wechatpay.merchantId'), //直连商户号
                        'out_trade_no' => $data['out_trade_no'], //商户订单号
                        'appid'        => config('config.wechatpay.appId'), //应用ID
                        'description'  => $data['description'], //商品描述
                        'notify_url'   => config('config.wechatpay.notifyUrl'), //通知地址
                        'amount'       => [
                            'total'    => $data['total'], //总金额
                            'currency' => 'CNY' //货币类型
                        ],
                    ]
                ]);
            //3. 处理响应或异常
            if ($resp->getStatusCode() != 200) throw new BadRequestException(['errorMessage' => $resp->getReasonPhrase()]);
            $result = $resp->getBody();
            $array = json_decode((string)$result, true);
            //APP调起支付 https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_2_4.shtml
            $params = [
                'appid'     => config('config.wechatpay.appId'), //应用ID
                'timestamp' => (string)Formatter::timestamp(), //时间戳
                'noncestr'  => Formatter::nonce(), //随机字符串
                'prepayid'     => $array['prepay_id'], //预支付交易会话ID
            ];
            $params += [
                'sign' => Rsa::sign(Formatter::joinedByLineFeed(...array_values($params)), Rsa::from('file://' . config('config.wechatpay.merchantPrivateKey'))), //签名
                'partnerid'     => config('config.wechatpay.merchantId'), //商户号
                'package'   => 'Sign=WXPay', //订单详情扩展字符串
            ];
            return $params;
            //return self::generateSignature($params);
        } catch (Exception $e) {
            throw new BadRequestException(['errorMessage' => $e->getMessage()]);
        }
    }

    /*
     * JSAPI支付
     * https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_1.shtml
     */
    public static function jsapiPay($data = [])
    {
        //1. 设置参数
        $instance = self::getOptions();

        try {
            //2. 发送请求
            $resp = $instance
                ->chain('v3/pay/transactions/jsapi')
                ->post([
                    'json' => [
                        'mchid'        => config('config.wechatpay.merchantId'), //直连商户号
                        'out_trade_no' => $data['out_trade_no'], //商户订单号
                        'appid'        => config('config.wechatoauth.appId'), //应用ID
                        'description'  => $data['description'], //商品描述
                        'notify_url'   => config('config.wechatpay.notifyUrl'), //通知地址
                        'amount'       => [
                            'total'    => $data['total'], //总金额
                            'currency' => 'CNY' //货币类型
                        ],
                        'payer'        => [
                            'openid'   => $data['openid'], //用户标识
                        ],
                    ]
                ]);
            //3. 处理响应或异常
            if ($resp->getStatusCode() != 200) throw new BadRequestException(['errorMessage' => $resp->getReasonPhrase()]);
            $result = $resp->getBody();
            $array = json_decode((string)$result, true);
            //JSAPI调起支付 https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_4.shtml
            $params = [
                'appId'     => config('config.wechatoauth.appId'), //应用ID
                'timeStamp' => (string)Formatter::timestamp(), //时间戳
                'nonceStr'  => Formatter::nonce(), //随机字符串
                'package'   => 'prepay_id='.$array['prepay_id'], //订单详情扩展字符串
            ];
            $params += [
                'paySign' => Rsa::sign(Formatter::joinedByLineFeed(...array_values($params)), Rsa::from('file://' . config('config.wechatpay.merchantPrivateKey'))), //签名
                'signType' => 'RSA' //签名方式
            ];
            return $params;
            //return self::generateSignature($params);
        } catch (Exception $e) {
            throw new BadRequestException(['errorMessage' => $e->getMessage()]);
        }
    }

    /*
     * 小程序支付
     * https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_5_1.shtml
     */
    public static function miniPay($data = [])
    {
        //1. 设置参数
        $instance = self::getOptions();

        try {
            //2. 发送请求
            $resp = $instance
                ->chain('v3/pay/transactions/jsapi')
                ->post([
                    'json' => [
                        'mchid'        => config('config.wechatpay.merchantId'), //直连商户号
                        'out_trade_no' => $data['out_trade_no'], //商户订单号
                        'appid'        => config('config.wechatoauth.appId'), //应用ID
                        'description'  => $data['description'], //商品描述
                        'notify_url'   => config('config.wechatpay.notifyUrl'), //通知地址
                        'amount'       => [
                            'total'    => $data['total'], //总金额
                            'currency' => 'CNY' //货币类型
                        ],
                        'payer'        => [
                            'openid'   => $data['openid'], //用户标识
                        ],
                    ]
                ]);
            //3. 处理响应或异常
            if ($resp->getStatusCode() != 200) throw new BadRequestException(['errorMessage' => $resp->getReasonPhrase()]);
            $result = $resp->getBody();
            $array = json_decode((string)$result, true);
            //小程序调起支付 https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_5_4.shtml
            $params = [
                'appId'     => config('config.wechatoauth.appId'), //小程序ID
                'timeStamp' => (string)Formatter::timestamp(), //时间戳
                'nonceStr'  => Formatter::nonce(), //随机字符串
                'package'   => 'prepay_id='.$array['prepay_id'], //订单详情扩展字符串
            ];
            $params += [
                'paySign' => Rsa::sign(Formatter::joinedByLineFeed(...array_values($params)), Rsa::from('file://' . config('config.wechatpay.merchantPrivateKey'))), //签名
                'signType' => 'RSA' //签名方式
            ];
            return $params;
            //return self::generateSignature($params);
        } catch (Exception $e) {
            throw new BadRequestException(['errorMessage' => $e->getMessage()]);
        }
    }

    /*
     * 验证签名
     */
    public static function checkSignature($data = [])
    {
        try {
            $inWechatpaySignature = $data['inWechatpaySignature'];// 请根据实际情况获取
            $inWechatpayTimestamp = $data['inWechatpayTimestamp'];// 请根据实际情况获取
            $inWechatpaySerial = $data['inWechatpaySerial'];// 请根据实际情况获取
            $inWechatpayNonce = $data['inWechatpayNonce'];// 请根据实际情况获取
            $inBody = $data['inBody'];// 请根据实际情况获取，例如: file_get_contents('php://input');

            $apiv3Key = config('config.wechatpay.apiV3key');;// 在商户平台上设置的APIv3密钥

            // 根据通知的平台证书序列号，查询本地平台证书文件，
            $platformPublicKeyInstance = Rsa::from('file://' . config('config.wechatpay.platformCertificate'), Rsa::KEY_TYPE_PUBLIC);

            // 检查通知时间偏移量，允许5分钟之内的偏移
            $timeOffsetStatus = 300 >= abs(Formatter::timestamp() - (int)$inWechatpayTimestamp);
            $verifiedStatus = Rsa::verify(
                // 构造验签名串
                Formatter::joinedByLineFeed($inWechatpayTimestamp, $inWechatpayNonce, $inBody),
                $inWechatpaySignature,
                $platformPublicKeyInstance
            );
            if ($timeOffsetStatus && $verifiedStatus) {
                // 转换通知的JSON文本消息为PHP Array数组
                $inBodyArray = (array)json_decode($inBody, true);
                // 使用PHP7的数据解构语法，从Array中解构并赋值变量
                ['resource' => [
                    'ciphertext'      => $ciphertext,
                    'nonce'           => $nonce,
                    'associated_data' => $aad
                ]] = $inBodyArray;
                // 加密文本消息解密
                $inBodyResource = AesGcm::decrypt($ciphertext, $apiv3Key, $nonce, $aad);
                // 把解密后的文本转换为PHP Array数组
                $inBodyResourceArray = json_decode($inBodyResource, true);
                return $inBodyResourceArray;// 返回解密后的结果
            }
            throw new BadRequestException(['errorMessage' => '签名验证失败']);
        } catch (Exception $e) {
            throw new BadRequestException(['errorMessage' => $e->getMessage()]);
        }
    }

    /*
     * 解密
     * https://pay.weixin.qq.com/wiki/doc/apiv3/wechatpay/wechatpay4_2.shtml
     */
    public static function decrypt($cert)
    {
        try {
            // 返回解密后的数据
            return AesGcm::decrypt($cert['ciphertext'], config('config.wechatpay.apiV3key'), $cert['nonce'], $cert['associated_data']);
        } catch (Exception $e) {
            throw new BadRequestException(['errorMessage' => $e->getMessage()]);
        }
    }

    /*
     * 加密
     * https://pay.weixin.qq.com/wiki/doc/apiv3/wechatpay/wechatpay4_3.shtml
     */
    public static function encrypt($msg)
    {
        try {
            // 从本地文件中加载「微信支付平台证书」，用来验证微信支付应答的签名
            $platformCertificateFilePath = 'file://' . config('config.wechatpay.platformCertificate');
            $platformPublicKeyInstance = Rsa::from($platformCertificateFilePath, Rsa::KEY_TYPE_PUBLIC);

            return Rsa::encrypt($msg, $platformPublicKeyInstance);
        } catch (Exception $e) {
            throw new BadRequestException(['errorMessage' => $e->getMessage()]);
        }
    }


}