<?php
declare(strict_types=1);

namespace app\common\service;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

/*
 * 邮箱
 */
class Email
{
    //email 配置
    private static $config = [
        'host' => 'smtp.263.net', //邮箱服务器域名
        'port' => '465', //邮箱服务器端口
        'username' => 'tec08@goldencell.biz', //邮箱服务器登录账号，注册一个邮箱账号即可，但需要进行邮箱设置开启smtp协议
        'password' => 'FFd2d49152deC3d4', //邮箱服务器登录密码，有的邮件服务器是授权码
        'sender' => [
            'tec08@goldencell.biz' => 'Goldencell', //邮件发送者, 格式: '邮箱' => '名称'
        ],
    ];

    /*
     * 发送邮件
     * @param $sender 寄件人
     * @param $receiver 收件人
     * @param $subject 主题
     * @param $body 正文
     * @param $attach 附件
     */
    public static function send(array $sender = [], array $receiver = [], string $subject = '', string $body = '', string $attach = '') :bool
    {
        // Create the Transport
        $transport = (new Swift_SmtpTransport(self::$config['host'], self::$config['port'])) //邮箱服务器
        ->setUsername(self::$config['username']) //登录账号
        ->setPassword(self::$config['password']); //登录密码

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new Swift_Message($subject))
            ->setFrom($sender) //邮件发送者，数组形式支持多个，例如： ['sender@domain.org', 'other@domain.org' => 'A name']
            ->setTo($receiver) //邮件接收者，数组形式支持多个，例如： ['receiver@domain.org', 'other@domain.org' => 'B name']
            //->attach($attach) //添加附件
            ->setBody($body); //邮件正文，可以包含html标签


        // Send the message
        $result = $mailer->send($message); //如果发送成功，会返回成功发送的数量
        return $result > 0 ? true : false;
    }
}
