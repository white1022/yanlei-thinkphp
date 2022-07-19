<?php
declare (strict_types = 1);

namespace app\common\service;

use Alipay\EasySDK\Kernel\Config;
use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Kernel\Util\ResponseChecker;
use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Express as ExpressModel;
use think\Exception;
use think\facade\Log;

/*
 * https://github.com/alipay/alipay-easysdk/tree/master/php
 */
class AliPay
{
    /*
     * 设置参数
     * https://open.alipay.com/api/detail?abilityCode=SM010000000000001001
     */
    private static function getOptions()
    {
        $options = new Config();
        $options->protocol = 'https';
        $options->gatewayHost = 'openapi.alipay.com';
        $options->signType = 'RSA2';

        $options->appId = config('config.alipay.appId');

        // 为避免私钥随源码泄露，推荐从文件中读取私钥字符串而不是写入源码中
        $options->merchantPrivateKey = config('config.alipay.merchantPrivateKey');

        // $options->alipayCertPath = '<-- 请填写您的支付宝公钥证书文件路径，例如：/foo/alipayCertPublicKey_RSA2.crt -->';
        // $options->alipayRootCertPath = '<-- 请填写您的支付宝根证书文件路径，例如：/foo/alipayRootCert.crt" -->';
        // $options->merchantCertPath = '<-- 请填写您的应用公钥证书文件路径，例如：/foo/appCertPublicKey_2019051064521003.crt -->';

        //注：如果采用非证书模式，则无需赋值上面的三个证书路径，改为赋值如下的支付宝公钥字符串即可
        $options->alipayPublicKey = config('config.alipay.alipayPublicKey');

        //可设置异步通知接收服务地址（可选）
        $options->notifyUrl = config('config.alipay.notifyUrl');

        //可设置AES密钥，调用AES加解密相关接口时需要（可选）
        $options->encryptKey = config('config.alipay.encryptKey');



        return $options;
    }

    /*
     * APP支付
     * https://github.com/alipay/alipay-easysdk/tree/master/php
     */
    public static function appPay($data = [])
    {
        //1. 设置参数（全局只需设置一次）
        Factory::setOptions(self::getOptions());

        try {
            //2. 发起API调用
            $result = Factory::payment()->app()->pay($data['subject'], $data['out_trade_no'], $data['total_amount']);
            $responseChecker = new ResponseChecker();
            //3. 处理响应或异常
            if ($responseChecker->success($result)) {
                return $result;
            } else {
                throw new BadRequestException(['errorMessage' => $result->msg."，".$result->subMsg]);
            }
        } catch (Exception $e) {
            throw new BadRequestException(['errorMessage' => $e->getMessage()]);
        }
    }

    /*
     * 异步通知验签
     * https://opendocs.alipay.com/open/204/105301
     */
    public static function checkSignature($data = [])
    {
        //1. 设置参数（全局只需设置一次）
        Factory::setOptions(self::getOptions());

        try {
            //2. 发起API调用
            return Factory::payment()->common()->verifyNotify($data);
        } catch (Exception $e) {
            //记录日志
            Log::write('支付宝回调成功，验签发生错误，错误原因：' . $e->getMessage());
        }
    }

    /*
     * 退款
     * https://opendocs.alipay.com/open/02e7go
     */
    public static function refund($data = [])
    {
        //1. 设置参数（全局只需设置一次）
        Factory::setOptions(self::getOptions());

        try {
            //2. 发起API调用
            $result = Factory::payment()->common()->refund($data['out_trade_no'], $data['refund_amount']);
            $responseChecker = new ResponseChecker();
            //3. 处理响应或异常
            if ($responseChecker->success($result)) {
                return $result;
            } else {
                throw new BadRequestException(['errorMessage' => $result->msg."，".$result->subMsg]);
            }
        } catch (Exception $e) {
            throw new BadRequestException(['errorMessage' => $e->getMessage()]);
        }
    }

    /*
     * PC支付
     * https://opendocs.alipay.com/open/028r8t?scene=22
     */
    public static function pcPay($data = [])
    {
        //1. 设置参数（全局只需设置一次）
        Factory::setOptions(self::getOptions());

        try {
            //2. 发起API调用
            $result = Factory::payment()->page()->pay($data['subject'], $data['out_trade_no'], $data['total_amount'], $data['return_url']);
            $responseChecker = new ResponseChecker();
            //3. 处理响应或异常
            if ($responseChecker->success($result)) {
                return $result;
            } else {
                throw new BadRequestException(['errorMessage' => $result->msg."，".$result->subMsg]);
            }
        } catch (Exception $e) {
            throw new BadRequestException(['errorMessage' => $e->getMessage()]);
        }
    }

}