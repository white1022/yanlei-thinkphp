<?php
declare(strict_types=1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\service\Redis as RedisService;

/*
 * 微信JS-SDK
 * https://developers.weixin.qq.com/miniprogram/dev/api-backend/
 */
class WeChatJsSdk
{
    //私有属性
    private $appId;

    private $appSecret;

    //构造方法
    public function __construct($appId = '', $appSecret = '')
    {
        $this->appId = empty($appId) ? config('config.wechatoauth.appId') : $appId;
        $this->appSecret = empty($appSecret) ? config('config.wechatoauth.appSecret') : $appSecret;
    }

    /*
     * 获取电话号码
     * 注意：该 code 是微信小程序调用 bindgetphonenumber 事件回调获取的 code，每个 code 只能使用一次，code的有效期为5min
     * 官方文档：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/phonenumber/phonenumber.getPhoneNumber.html
     */
    public function getPhoneNumber(string $code = '')
    {
        $accessToken = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=$accessToken";
        $method = 'POST';
        $param = json_encode([
            'code' => $code,
        ]);
        $header = [
            'Content-Type:application/json; charset=UTF-8',
            'Content-Length:' . strlen($param),
        ];
        $res = curl($url, $method, $header, $param);
        if($res['errcode'] != 0) throw new BadRequestException(['errorMessage' => $res['errmsg']]);
        return $res['phone_info'];
    }

    /*
     * 登录凭证校验
     * 注意：该 code 是微信小程序调用 wx.login 方法返回的 code，每个 code 只能使用一次，code的有效期为5min
     * 官方文档：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/login/auth.code2Session.html
     */
    public function code2Session(string $code = '')
    {
        //获取redis实例
        $redis = RedisService::getInstance();
        //读取缓存
        $openId = $redis->get($code);
        //判断下，防止重复请求导致code被使用
        if(!$openId){
            $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$this->appId&secret=$this->appSecret&js_code=$code&grant_type=authorization_code";
            $method = 'GET';
            $header = [];
            $param = [];
            $res = curl($url, $method, $header, $param);

            if(!isset($res['openid'])) throw new BadRequestException(['errorMessage' => $res['errmsg']]);

            //写入缓存
            $redis->setex($code, 300 - 10, $res['openid']); //缓存300秒，因为请求也需要时间所以不建议直接使用300
            $redis->set($res['openid'], $res['session_key']); //缓存会话密钥，用于对用户数据进行加密签名

            $openId = $res['openid'];
        }
        return $openId;
    }

    /*
     * 数据签名校验
     * 官方文档：https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/signature.html
     */
    public function dataSignatureValidation(string $openId = '', string $rawData = '', string $signature = '')
    {
        //获取redis实例
        $redis = RedisService::getInstance();
        //读取缓存
        $sessionKey = $redis->get($openId);
        if(!$sessionKey) throw new BadRequestException(['errorMessage' => 'session_key不存在']);
        $signature2 = sha1("$rawData$sessionKey");
        return $signature2 === $signature ? true : false;
    }

    /*
     * 获取签名包
     * 官方文档：https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html#62
     */
    public function getSignPackage() {
        $jsApiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = random_string(16, 1);

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsApiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        return [
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        ];
    }

    /*
     * 获取调用微信JS接口的临时票据
     * 官方文档：https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html#62
     */
    private function getJsApiTicket() {
        //获取redis实例
        $redis = RedisService::getInstance();
        //读取缓存
        $key = 'JSAPITICKET';
        $jsApiTicket = $redis->get($key);
        if(!$jsApiTicket){
            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $method = 'GET';
            $header = [];
            $param = [];
            $res = curl($url, $method, $header, $param);

            $jsApiTicket = $res['ticket'] ?? '';
            if($jsApiTicket){
                //写入缓存
                $redis->setex($key, $res['expires_in'] - 10, $jsApiTicket); //缓存7000秒，因为请求也需要时间所以不建议直接使用7200
            }
        }
        return $jsApiTicket;
    }

    /*
     * 获取访问令牌
     * 官方文档：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/access-token/auth.getAccessToken.html
     */
    private function getAccessToken() {
        //获取redis实例
        $redis = RedisService::getInstance();
        //读取缓存
        $key = 'ACCESSTOKEN';
        $accessToken = $redis->get($key);
        if(!$accessToken){
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $method = 'GET';
            $header = [];
            $param = [];
            $res = curl($url, $method, $header, $param);

            $accessToken = $res['access_token'] ?? '';
            if($accessToken){
                //写入缓存
                $redis->setex($key, $res['expires_in'] - 10, $accessToken); //缓存7000秒，因为请求也需要时间所以不建议直接使用7200
            }
        }
        return $accessToken;
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return json_decode($res, true);
    }


}
