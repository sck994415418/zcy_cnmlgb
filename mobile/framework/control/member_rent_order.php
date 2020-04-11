<?php
/**
 * 我的订单
 *
 * @好商城V4 (c) 2015-2016 33hao Inc.
 * @license    http://www.haoid.cn
 * @link       交流群号：216611541
 * @since      好商城提供技术支持 授权请购买shopnc授权
 */

defined('InShopNC') or exit('Access Invalid!');

class member_rent_orderControl extends mobileMemberControl {

	public function __construct(){
		parent::__construct();
	}

    /**
     * 订单列表
     */
    public function order_listOp() {

        $ownShopIds = Model('store')->getOwnShopIds();

        $model_order = Model('rent_order');
        
        $condition = array();
        $condition['buyer_id'] = $this->member_info['member_id'];
        if (preg_match('/^\d{10,20}$/',$_POST['order_key'])) {
            $condition['order_sn'] = $_POST['order_key'];
        } elseif ($_POST['order_key'] != '') {
            $condition['goods_name'] = array('like','%'.$_POST['order_key'].'%');
        }
        if($_POST['state_type'] == 'state_new'){
            $condition['order_state'] = 10;
        }
        if($_POST['state_type'] == 'state_pay'){
            $condition['order_state'] = array('gt',10);
        }  
        // $order_list = $model_order->getOrderList($condition, $this->page, '*', 'order_id desc');
        $field = "rorder_id,buyer_id,buyer_name,buyer_phone,buyer_address,add_time,goods_id,order_state,model,other";
        $order_list = Model()->table("rent_order")->field($field)->where($condition)->select();
        foreach ($order_list as $key => $value) {
            $order_list[$key]['add_time'] = date('Y-m-d H:i:s',$value['add_time']);
        }
        output_data(array('order_list' => $order_list));
    }

    public function order_infoOp() {
        $order_id = intval($_GET['order_id']);
        if ($order_id <= 0) {
            output_error('订单不存在');
        }
        $model_vr_order = Model('rent_order');
        $condition = array();
        $condition['rorder_id'] = $order_id;
        // $condition['buyer_id'] = $this->member_info['member_id'];
        // $order_info = $model_vr_order->getOrderInfo($condition);
        $field = "rorder_id,buyer_id,buyer_name,buyer_phone,buyer_address,add_time,goods_id,order_state,model,other";
        $order_info = Model()->table("rent_order")->field($field)->where($condition)->find();
        $goodmap['goods_id'] = $order_info['goods_id'];
        $goods = Model()->table("goods")->field("goods_name,goods_jingle,store_name,goods_price,goods_image,store_id")->where($goodmap)->find();
        $order_info['add_time'] = date('Y-m-d H:i:s',$order_info['add_time']);
        $order_info['goods_name'] = $goods['goods_name'];
        $order_info['store_name'] = $goods['store_name'];
        $order_info['goods_jingle'] = $goods['goods_jingle'];
        $order_info['goods_price'] = $goods['goods_price'];
        $order_info['goods_image'] =  cthumb($goods['goods_image'], 240, $goods['store_id']);
        if (empty($order_info)) {
            output_error('订单不存在');
        }

        output_data($order_info);   
    }

    /**
     * 取消订单
     */
    public function order_cancelOp() {
        $model_vr_order = Model('rent_order');
        $condition = array();
        $condition['order_id'] = intval($_POST['order_id']);
        $condition['buyer_id'] = $this->member_info['member_id'];
        $order_info	= $model_vr_order->getOrderInfo($condition);

        $if_allow = $model_vr_order->getOrderOperateState('buyer_cancel',$order_info);
        if (!$if_allow) {
            output_data('无权操作');
        }

        $logic_vr_order = Logic('rent_order');
        $result = $logic_vr_order->changeOrderStateCancel($order_info,'buyer', '其它原因');

        if(!$result['state']) {
            output_data($result['msg']);
        } else {
            output_data('1');
        }
    }

    /**
     * 发送兑换码到手机
     */
    public function resendOp() {
        if (!preg_match('/^[\d]{11}$/',$_POST['buyer_phone'])) {
            output_error('请正确填写手机号');
        }
        $order_id   = intval($_POST['order_id']);
        if ($order_id <= 0) {
            output_error('参数错误');
        }

        $model_vr_order = Model('rent_order');

        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $order_info = $model_vr_order->getOrderInfo($condition);
        if (empty($order_info) && $order_info['order_state'] != ORDER_STATE_PAY) {
            output_error('订单信息发生错误');
        }
        if ($order_info['vr_send_times'] >= 5) {
            output_error('您发送的次数过多，无法发送');
        }

        //发送兑换码到手机
        $param = array('order_id'=>$order_id,'buyer_id'=>$this->member_info['member_id'],'buyer_phone'=>$_POST['buyer_phone'],'goods_name'=>$order_info['goods_name']);
        QueueClient::push('sendVrCode', $param);

        $model_vr_order->editOrder(array('vr_send_times'=>array('exp','vr_send_times+1')),array('order_id'=>$order_id));

        output_data('1');
    }
}
