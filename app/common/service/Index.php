<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\model\Feedback as FeedbackModel;
use app\common\model\Goods as GoodsModel;
use app\common\model\Order as OrderModel;
use app\common\model\User as UserModel;
use think\facade\Db;

class Index
{
    /*
     * 获取待办事项
     */
    public static function getBacklog() :array
    {
        //待发货订单
        $unfilledOrderCount = OrderModel::where('status', '=', 2)
            ->count();
        //待处理意见反馈
        $pendingFeedbackCount = FeedbackModel::where('status', '=', 0)
            ->count();

        return [
            'unfilledOrderCount' => $unfilledOrderCount,
            'pendingFeedbackCount' => $pendingFeedbackCount,
        ];
    }

    /*
    * 获取数据统计
    */
    public static function getStatistics() :array
    {
        //用户量
        $userCount = UserModel::count();
        //商品量
        $goodsCount = GoodsModel::count();
        //订单量
        $orderCount = OrderModel::count();

        return [
            'userCount' => $userCount,
            'goodsCount' => $goodsCount,
            'orderCount' => $orderCount,
        ];
    }

    /*
    * 获取订单图表
    */
    public static function getOrderChart() :array
    {
        //前7天数据
        $data = [
            ['date'=>date('Y-m-d', strtotime ( "-7 day")), 'payment_count'=>0, 'delivery_count'=>0, 'receipt_count'=>0],
            ['date'=>date('Y-m-d', strtotime ( "-6 day")), 'payment_count'=>0, 'delivery_count'=>0, 'receipt_count'=>0],
            ['date'=>date('Y-m-d', strtotime ( "-5 day")), 'payment_count'=>0, 'delivery_count'=>0, 'receipt_count'=>0],
            ['date'=>date('Y-m-d', strtotime ( "-4 day")), 'payment_count'=>0, 'delivery_count'=>0, 'receipt_count'=>0],
            ['date'=>date('Y-m-d', strtotime ( "-3 day")), 'payment_count'=>0, 'delivery_count'=>0, 'receipt_count'=>0],
            ['date'=>date('Y-m-d', strtotime ( "-2 day")), 'payment_count'=>0, 'delivery_count'=>0, 'receipt_count'=>0],
            ['date'=>date('Y-m-d', strtotime ( "-1 day")), 'payment_count'=>0, 'delivery_count'=>0, 'receipt_count'=>0],
        ];
        //查询前7天付款订单数据
        $payment_order = Db::query("SELECT
	FROM_UNIXTIME( payment_time, '%Y-%m-%d' ) date,
	count(*) count
FROM
	( SELECT * FROM tp_order WHERE UNIX_TIMESTAMP( DATE_SUB( CURDATE(), INTERVAL 7 DAY )) <= payment_time ) AS payment_order
GROUP BY
	date");
        //查询前7天发货订单数据
        $delivery_order = Db::query("SELECT
	FROM_UNIXTIME( delivery_time, '%Y-%m-%d' ) date,
	count(*) count
FROM
	( SELECT * FROM tp_order WHERE UNIX_TIMESTAMP( DATE_SUB( CURDATE(), INTERVAL 7 DAY )) <= delivery_time ) AS delivery_order
GROUP BY
	date");
        //查询前7天收货订单数据
        $receipt_order = Db::query("SELECT
	FROM_UNIXTIME( receipt_time, '%Y-%m-%d' ) date,
	count(*) count
FROM
	( SELECT * FROM tp_order WHERE UNIX_TIMESTAMP( DATE_SUB( CURDATE(), INTERVAL 7 DAY )) <= receipt_time ) AS receipt_order
GROUP BY
	date");
        foreach ($data as &$item) {
            foreach ($payment_order as $item1) {
                if($item['date'] == $item1['date']){
                    $item['payment_count'] = $item1['count'];
                }
            }
            foreach ($delivery_order as $item2) {
                if($item['date'] == $item2['date']){
                    $item['delivery_count'] = $item2['count'];
                }
            }
            foreach ($receipt_order as $item3) {
                if($item['date'] == $item3['date']){
                    $item['receipt_count'] = $item3['count'];
                }
            }
        }

        return [
            'date' => array_column($data, 'date'), //日期
            'payment_count' => array_column($data, 'payment_count'), //付款量
            'delivery_count' => array_column($data, 'delivery_count'), //发货量
            'receipt_count' => array_column($data, 'receipt_count'), //收货量
        ];
    }

    /*
    * 获取用户图表
    */
    public static function getUserChart() :array
    {
        //前7天数据
        $data = [
            ['date'=>date('Y-m-d', strtotime ( "-7 day")), 'create_count'=>0],
            ['date'=>date('Y-m-d', strtotime ( "-6 day")), 'create_count'=>0],
            ['date'=>date('Y-m-d', strtotime ( "-5 day")), 'create_count'=>0],
            ['date'=>date('Y-m-d', strtotime ( "-4 day")), 'create_count'=>0],
            ['date'=>date('Y-m-d', strtotime ( "-3 day")), 'create_count'=>0],
            ['date'=>date('Y-m-d', strtotime ( "-2 day")), 'create_count'=>0],
            ['date'=>date('Y-m-d', strtotime ( "-1 day")), 'create_count'=>0],
        ];
        //查询前7天注册用户数据
        $create_user = Db::query("SELECT
	FROM_UNIXTIME( create_time, '%Y-%m-%d' ) date,
	count(*) count
FROM
	( SELECT * FROM tp_user WHERE UNIX_TIMESTAMP( DATE_SUB( CURDATE(), INTERVAL 7 DAY )) <= create_time ) AS create_user
GROUP BY
	date");
        foreach ($data as &$item) {
            foreach ($create_user as $item1) {
                if($item['date'] == $item1['date']){
                    $item['create_count'] = $item1['count'];
                }
            }
        }

        return [
            'date' => array_column($data, 'date'), //日期
            'create_count' => array_column($data, 'create_count'), //注册量
        ];
    }
}