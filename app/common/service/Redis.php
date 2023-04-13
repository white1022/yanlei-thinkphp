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

    //redis 参数
    private static $options = [
        'host' => '127.0.0.1',
        'port' => 6379,
        //'password' => '',
    ];

    //构造方法私有化，防止外部创建实例
    private function __construct()
    {
    }

    //克隆方法私有化，防止复制实例
    private function __clone()
    {
    }

    //公有方法，用于获取实例
    public static function getInstance(array $options = []) :Client
    {
        if (empty($options)) {
            //判断实例有无创建，没有的话创建实例
            if(!(self::$instance instanceof Client)){
                self::$instance = new Client(self::$options);
            }
        } else {
            //合并参数
            self::$options = array_merge(self::$options, $options);
            self::$instance = new Client(self::$options);
        }
        // 返回单例
        return self::$instance;
    }
}
