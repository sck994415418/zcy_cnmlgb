<?php
/**
 * 虚拟订单模型
 *
 *
 *
 *
 * by 33hao 好商城V3  www.haoid.cn 开发
 */
defined('InShopNC') or exit('Access Invalid!');
class rent_orderModel extends Model {

    /**
     * 取单条订单信息
     *
     * @param array $condition
     * @return unknown
     */
    public function getOrderInfo($condition = array(), $fields = '*', $master = false) {
        $order_info = $this->table('rent_order')->field($fields)->where($condition)->master($master)->find();
        $order_info['order_sn'] = $order_info['rorder_id'];
        if (empty($order_info)) {
            return array();
        }
        $gmap['goods_id'] = $order_info['goods_id'];
        $goods = Model()->table("goods_rent")->where($gmap)->find();
        $order_info = array_merge($order_info,$goods);
        
        return $order_info;
    }

    /**
     * 新增订单
     * @param array $data
     * @return int 返回 insert_id
     */
    public function addOrder($data) {
        $insert = $this->table('rent_order')->insert($data);
        return $insert;
    }

    /**
     * 更改订单信息
     *
     * @param array $data
     * @param array $condition
     */
    public function editOrder($data, $condition, $limit = '') {
        return $this->table('rent_order')->where($condition)->limit($limit)->update($data);
    }

	/**
	 * 根据虚拟订单取没有使用的兑换码列表
	 *
	 * @param
	 * @return array
	 */
	public function getCodeRefundList($order_list = array()) {
	    if (!empty($order_list) && is_array($order_list)) {
	        $order_ids = array();//订单编号数组
    	    foreach ($order_list as $key => $value) {
    	        $order_id = $value['rorder_id'];
    	        $order_ids[$order_id] = $key;
    	    }
    	    $condition = array();
    	    $condition['rorder_id'] = array('in', array_keys($order_ids));
    	    $condition['refund_lock'] = '0';//退款锁定状态:0为正常(能退款),1为锁定(待审核),2为同意
    	    $code_list = $this->getCodeList($condition);
    	    if (!empty($code_list) && is_array($code_list)) {
        	    foreach ($code_list as $key => $value) {
        	        $order_id = $value['rorder_id'];//虚拟订单编号
        	    }
    	    }
	    }
		return $order_list;
    }


    /**
     * 取得订单列表(所有)
     * @param unknown $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @return array
     */
    public function getOrderList($condition, $pagesize = '', $field = '*', $order = 'rorder_id desc', $limit = ''){
        $list = $this->table('rent_order')->field($field)->where($condition)->page($pagesize)->order($order)->limit($limit)->select();
        if (empty($list)) return array();
        foreach ($list as $key => $order) {
            $list[$key]['order_sn'] = $order_info['rorder_id'];
            $gmap['goods_id'] = $order['goods_id'];
            $goods = Model()->table("goods_rent")->where($gmap)->find();
            $list[$key] = array_merge($order,$goods);
        }

        return $list;
    }

    /**
     * 取得订单状态文字输出形式
     *
     * @param array $order_info 订单数组
     * @return string $order_state 描述输出
     */
    private function _orderState($order_state) {
        switch ($order_state) {
        	case ORDER_STATE_CANCEL:
        	    $order_state = '<span style="color:#999">已取消</span>';
                $order_state_text = '已取消';
        	    break;
        	case ORDER_STATE_NEW:
        	    $order_state = '<span style="color:#36C">待付款</span>';
                $order_state_text = '待付款';
        	    break;
        	case ORDER_STATE_PAY:
        	    $order_state = '<span style="color:#999">已支付</span>';
                $order_state_text = '已支付';
        	    break;
        	case ORDER_STATE_SUCCESS:
        	    $order_state = '<span style="color:#999">已完成</span>';
                $order_state_text = '已完成';
        	    break;
        }
        return array($order_state, $order_state_text);;
    }



    /**
     * 返回是否允许某些操作
     * @param string $operate
     * @param array $order_info
     */
    public function getOrderOperateState($operate, $order_info){

        if (!is_array($order_info) || empty($order_info)) return false;

        switch ($operate) {

                //买家取消订单
        	case 'buyer_cancel':
        	    $state = $order_info['order_state'] == ORDER_STATE_NEW;
        	    break;

        	    //商家取消订单
        	case 'store_cancel':
        	    $state = $order_info['order_state'] == ORDER_STATE_NEW;
        	    break;

        	    //平台取消订单
        	case 'system_cancel':
        	    $state = $order_info['order_state'] == ORDER_STATE_NEW;
        	    break;

        	    //平台收款
        	case 'system_receive_pay':
        	    $state = $order_info['order_state'] == ORDER_STATE_NEW;
        	    break;

                //支付
        	case 'payment':
        	    $state = $order_info['order_state'] == ORDER_STATE_NEW;
        	    break;

    	       //评价
    	    case 'evaluation':
    	        $state = !$order_info['lock_state'] && $order_info['evaluation_state'] == '0' && $order_info['use_state'];
    	        break;

            	//买家退款
        	case 'refund':
        	    $state = false;
        	    $code_list = $order_info['code_list'];//没有使用的兑换码列表
        	    if (!empty($code_list) && is_array($code_list)) {
        	        if ($order_info['vr_indate'] > TIMESTAMP) {//有效期内的能退款
        	            $state = true;
        	        }
        	        if ($order_info['vr_invalid_refund'] == 1 && ($order_info['vr_indate'] + 60*60*24*CODE_INVALID_REFUND) > TIMESTAMP) {//兑换码过期后可退款
        	            $state = true;
        	        }
        	    }
        	    break;

        	    //分享
    	    case 'share':
    	        $state = true;
    	        break;
        }
        return $state;
    }

    /**
     * 订单详情页显示进行步骤
     * @param array $order_info
     */
    public function getOrderStep($order_info){
        if (!is_array($order_info) || empty($order_info)) return array();
        $step_list = array();
        // 第一步 下单完成
	    $step_list['step1'] = true;
	    //第二步 付款完成
	    $step_list['step2'] = !empty($order_info['payment_time']);
	    //第三步 兑换码使用中
	    $step_list['step3'] = !empty($order_info['payment_time']);
	    //第四步 使用完成或到期结束
	    $step_list['step4'] = $order_info['order_state'] == ORDER_STATE_SUCCESS;
        return $step_list;
    }

    /**
     * 取得订单数量
     * @param unknown $condition
     */
    public function getOrderCount($condition) {
        return $this->table('rent_order')->where($condition)->count();
    }
    
    /**
     * 订单销售记录 订单状态为20、30、40时
     * @param unknown $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
    public function getOrderAndOrderGoodsSalesRecordList($condition, $field="*", $page = 0, $order = 'rorder_id desc') {
        $condition['order_state'] = array('in', array(ORDER_STATE_PAY, ORDER_STATE_SUCCESS));
        return $this->getOrderList($condition, $field, $page, $order);
    }
}
