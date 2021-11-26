<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/*
 * 加密
 * 加密方式为 转json、然后加上一个随机6位的数字，之后base64
 */
function encryption($data)
{
    return base64_encode(json_encode($data) . rand(100000, 999999));
}

/*
 * 解密
 */
function decryption($data)
{
    return json_decode(substr(base64_decode($data), 0, -6), true);
}

/*
 * 生成无限极分类树
 */
function generateTree($array = []){
    /*$array = array(
        array('id' => 1, 'name' => '超级管理员', 'pid' => '0', 'level' =>0) ,
        array('id' => 2, 'name' => '二级组', 'pid' => '1', 'level' =>1) ,
        array('id' => 3, 'name' => '二级组', 'pid' => '1', 'level' =>1) ,
        array('id' => 4, 'name' => '三级组', 'pid' => '2', 'level' =>2) ,
        array('id' => 5, 'name' => '二级组', 'pid' => '1', 'level' =>1) ,
        array('id' => 6, 'name' => '三级组', 'pid' => '3', 'level' =>2) ,
    );*/

    //第一步 构造数据  （循环、函数 两种方法可以实现）
    $items = array_column($array,NULL,'id');

    //第二部 遍历数据 生成树状结构
    $tree = array();
    foreach($items as $key => $item){
        if(isset($items[$item['pid']])){
            $items[$item['pid']]['child'][] = &$items[$key];
        }else{
            $tree[] = &$items[$key];
        }
    }
    //这里树形结构已经生成 没有使用递归方法
    return $tree;
}

/*
 * 获取地区树
 * 数据 $data
 * 父级 $pid
 * 层级 $rank
 */
function getRegionTree($data, $pid=0, $rank=0){
    static $tree = array();
    foreach($data as $k=>$v){
        if($v['pid'] == $pid){
            $v['rank'] = $rank;
            $tree[]=$v;
            getRegionTree($data,$v['id'],$rank+1);
        }
    }
    //这里树形结构已经生成 使用递归方法
    return $tree;
}

/*
 * 获取分页信息
 * page  页码
 * limit 每页显示条数
 */
function get_page_limit()
{
    return [input('get.page', 1), input('get.limit', 15)];
}

/*
 * 返回请求数据
 * $code 状态码（200成功，400失败）
 * $message 提示消息（支持多语言）
 * $data 数据
 * $count 数据总数，可选项 如果是渲染layui的table则需要此字段
 */
function returnResponse($code = 200, $message = '', $data = [], $count = null)
{
    //dump($message);exit();
    $array['code'] = $code;
    $array['message'] = lang($message);
    $array['data'] = $data;
    !is_null($count) && $array['count'] = $count;
    return json($array, 200);
}

/*
 * 语言切换
 */
function change_lang($type)
{
    switch ($type) {
        case 'zh-cn':
            $lang = 'zh-cn';
            cookie('think_var', 'zh-cn');
            break;
        case 'en-us':
            $lang = 'en-us';
            cookie('think_var', 'en-us');
            break;
        default:
            $lang = '';
            break;
    }
    return $lang;
}

/*
 * 生成随机字符串
 * $length 随机字符串的长度
 * $type 类型：1大写+小写+数字，2大写，3小写，4数字
 */
function random_string($length = 16, $type = 1)
{
    $array = [
        1 => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        2 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        3 => 'abcdefghijklmnopqrstuvwxyz',
        4 => '0123456789',
    ];
    $chars = $array[$type];
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

/*
 * 获取剩余时间的 日,时,分,秒展示
 * $expire_time 到期时间
 */
function time2day_hour_min_second($expire_time){
    $time = $expire_time - time();
    $data = [];
    if($time > 0){
        $day = floor($time/86400);
        $hour = floor(($time - $day * 86400)/3600);
        $min = floor((($time - $day * 86400) - $hour * 3600)/60);
        $second = floor((($time - $day * 86400) - $hour * 3600) - $min * 60);

        $data['day'] = $day;
        $data['hour'] = $hour;
        $data['min'] = $min;
        $data['second'] = $second;
    }
    return $data;
}

/*
 * 字符串转十进制
 */
function str_to_hex($str){
    $hex="";
    for($i=0;$i<strlen($str);$i++){
        //str_pad 使用另一个字符串填充字符串为指定长度
        //dechex 十进制转换为十六进制
        $hex.=str_pad(dechex(ord($str[$i])), 2, "0", STR_PAD_LEFT);

    }
    return strtoupper($hex);
}

/*
 * crc16/ModBus校验
 * $str 需要校验的字符串 例:010313010001
 */
function crc16_ModBus($str)
{
    //pack 函数把数据装入一个二进制字符串
    $data = pack('H*', $str);
    $crc = 0xFFFF;
    for ($i = 0; $i < strlen($data); $i++) {
        $crc ^= ord($data[$i]);
        for ($j = 8; $j != 0; $j--) {
            if (($crc & 0x0001) != 0) {
                $crc >>= 1;
                $crc ^= 0xA001;
            } else {
                $crc >>= 1;
            }
        }
    }
    //sprintf 函数返回已格式化的字符串
    $crc_str = sprintf("%X", $crc);
    echo '$crc_str = ' . $crc_str . PHP_EOL;
    return $crc_str;
}

/*
 * 处理crc验证码的顺序
 */
function handle_crc_str($crc_str)
{
    $array = str_split($crc_str, 2);
    $new_crc_str = $array[1] . $array[0];
    echo '$new_crc_str = ' . $new_crc_str . PHP_EOL;
    return $new_crc_str;
}
