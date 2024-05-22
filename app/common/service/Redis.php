<?php
declare(strict_types=1);

namespace app\common\service;

use Predis\Client;

/*
 * 缓存
 */
class Redis
{
    //私有属性，用于保存实例
    private static $instance;

    //构造方法私有化，防止外部创建实例
    private function __construct()
    {
    }

    //克隆方法私有化，防止复制实例
    private function __clone()
    {
    }

    //公有方法，用于获取实例
    public static function getInstance(array $option = []) :Client
    {
        //redis 参数
        $param = [
            'host' => env('redis.host', '127.0.0.1'),
            'port' => env('redis.port', 6379),
            'password' => env('redis.password', null),
        ];

        if (empty($option)) {
            //判断实例有无创建，没有的话创建实例
            if(!(self::$instance instanceof Client)){
                self::$instance = new Client($param);
            }
        } else {
            //合并参数
            $param = array_merge($param, $option);
            self::$instance = new Client($param);
        }
        // 切换到指定的DB库
        self::$instance->select(env('redis.select', 0));
        // 返回单例
        return self::$instance;
    }
}
