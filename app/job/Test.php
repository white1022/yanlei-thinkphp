<?php
declare (strict_types = 1);

namespace app\job;

use think\queue\Job;
use think\facade\Log;

class Test
{
    /*
     * fire是消息队列默认调用的方法
     * $job 当前的任务对象
     * $data 发布任务时自定义的数据
     * https://packagist.org/packages/topthink/think-queue
     * https://packagist.org/packages/php-amqplib/php-amqplib
     * https://blog.csdn.net/qq_37544121/article/details/105225783
     * https://zhuanlan.zhihu.com/p/344026264
     */
    public function fire(Job $job, $data){

        //....这里执行具体的任务

        if ($job->attempts() > 3) {
            //通过这个方法可以检查这个任务已经重试了几次了
        }


        //如果任务执行成功后 记得删除任务，不然这个任务会重复执行，直到达到最大重试次数后失败后，执行failed方法
        $job->delete();

        // 也可以重新发布这个任务
        $job->release(10); //$delay为延迟时间，单位是秒

    }

    public function failed($data){

        // ...任务达到最大重试次数后，失败了
        Log::record('记录日志信息');
    }

}