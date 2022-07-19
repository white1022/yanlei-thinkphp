<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Aftermarket as AftermarketModel;
use app\common\model\Express as ExpressModel;
use app\common\model\Order as OrderModel;
use app\common\model\OrderGoods as OrderGoodsModel;
use app\common\model\User as UserModel;
use app\common\service\Admin as AdminService;
use app\common\service\Qrcode as QrcodeService;
use think\facade\Db;

class Order
{
    /*
     * 获取订单列表
     */
    public static function getOrderList() :array
    {
        list($page, $limit) = get_page_limit();
        $order_no = input('get.order_no', '');
        $status = input('get.status', '');
        $create_time_start = input('get.create_time_start', '');
        $create_time_end = input('get.create_time_end', '');
        $is_remind_delivery = input('get.is_remind_delivery', '');
        $aftermarket_status = input('get.aftermarket_status', '');
        $user_nickname = input('get.user_nickname', '');
        $user_mobile = input('get.user_mobile', '');

        $condition = [];
        if(!empty($order_no)){
            array_push($condition, ['order_no','=',$order_no]);
        }
        if(!empty($status)){
            array_push($condition, ['status','=',$status]);
        }
        if(!empty($create_time_start) && !empty($create_time_end)){
            array_push($condition, ['create_time','between',[strtotime($create_time_start), strtotime($create_time_end)]]);
        }
        if(!empty($is_remind_delivery)){
            array_push($condition, ['is_remind_delivery','=',$is_remind_delivery]);
        }
        if($aftermarket_status != ''){
            $order_id = AftermarketModel::where('status', '=', $aftermarket_status)->column('order_id');
            array_push($condition, ['id','in',$order_id]);
        }

        $condition2 = [];
        if(!empty($user_nickname)){
            array_push($condition2, ['nickname','like','%'.$user_nickname.'%']);
        }
        if(!empty($user_mobile)){
            array_push($condition2, ['mobile','like','%'.$user_mobile.'%']);
        }
        if(!empty($condition2)){
            $user_id = UserModel::where($condition2)->column('id');
            array_push($condition, ['user_id','in',$user_id]);
        }

        $list = OrderModel::with([
            'user',
            'address',
            'express',
            'orderGoods' => [
                'goodsSpecification',
                'goods' => [
                    'goodsSpecification',
                    'goodsParameter',
                ],
                'evaluate',
            ],
            'aftermarket',
        ])->where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        $total = OrderModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 删除订单信息
     */
    public static function deleteOrderInfo() :void
    {
        $ids = explode(',', input('post.id'));
        foreach ($ids as $id){
            $info = OrderModel::where('id', '=', $id)
                ->findOrEmpty();
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);

            $count = AftermarketModel::where('order_id', '=', $info->id)
                ->where('status', '=', 0)
                ->count();
            if($count) throw new BadRequestException(['errorMessage' => '订单正在售后中']);
            $count = AftermarketModel::where('order_id', '=', $info->id)
                ->where('status', '=', 1)
                ->where('refund_time', '=', 0)
                ->count();
            if($count) throw new BadRequestException(['errorMessage' => '订单正在退款中']);

            $res = $info->delete();
            if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
            //删除订单商品
            OrderGoodsModel::where('order_no', '=', $info->order_no)->delete();
            //删除售后
            AftermarketModel::where('order_id', '=', $info->id)->delete();
        }
    }

    /*
     * 获取订单信息
     */
    public static function getOrderInfo() :array
    {
        $info = OrderModel::with([
            'user',
            'address',
            'express',
            'orderGoods' => [
                'goodsSpecification',
                'goods' => [
                    'goodsSpecification',
                    'goodsParameter',
                ],
                'evaluate',
            ],
            'aftermarket',
        ])->where('id', '=', input('get.id', 0))
            ->findOrEmpty();
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        return $info->toArray();
    }

    /*
     * 订单发货
     */
    public static function orderDelivery() :void
    {
        $info = OrderModel::where('id', '=', input('post.id', 0))
            ->findOrEmpty();
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        if($info->status != 2) throw new BadRequestException(['errorMessage' => '订单不是待发货状态']);
        $info->express_id = input('post.express_id', 0);
        $info->express_no = input('post.express_no', '');
        $info->delivery_time = time();
        $info->status = 3;
        if(!$info->save()) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 获取快递列表
     */
    public static function getExpressList() :array
    {
        return ExpressModel::column('name', 'id');
    }

    /*
     * 获取打印预览数据
     */
    public static function getPrintPreview(int $adminId = 0) :array
    {
        $order_id =  input('get.id', '');

        $list = Db::query("SELECT 
    * 
FROM
	tp_order
	INNER JOIN tp_order_goods ON tp_order_goods.order_no = tp_order.order_no
	INNER JOIN tp_user ON tp_user.id = tp_order.user_id 
WHERE
	tp_order.id IN ( $order_id ) 
ORDER BY
	tp_order.create_time DESC");

        foreach ($list as &$item){
            //添加打印的照片上传二维码
            $item['print_picture_upload_qrcode'] = QrcodeService::getInstance([
                                                    'data'=>'https://index.meilanhu.vip/#/h5/index5?id='.$item['id'],
                                                    'labelText'=>'照片上传[ID:'.$item['id'].']',
                                                ])->dataUri();
            //添加打印的时间
            $item['print_time'] = time();
            //添加打印的管理员
            $admin = AdminService::getAdminInfoByCache($adminId);
            $item['print_administrator'] = $admin['nickname'];
        }

        return $list;
    }
    
}