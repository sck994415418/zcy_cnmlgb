 <?php
/**
 * 卖家商品咨询管理
 *
 *
 *
 **by 好商城V3 www.haoid.cn 运营版*/

defined('InShopNC') or exit('Access Invalid!');
class store_taskControl extends BaseSellerControl {
	public function __construct() {
		parent::__construct();
		Language::read('member_store_index');
	}
	/**
	 * 执行自动收货
	 */
	public function load_taskOp(){
		try {
			$model_order = Model('order');
			$condition = array();
			$where = array();
			$delay_time=TIMESTAMP-3600*24*7;
			$where['delay_time'] = array('elt', $delay_time);
			$where['order_state'] = 30;
			//@$update = $model_order->query("UPDATE `zmkj_order` SET finnshed_time= `delay_time`, order_state=".ORDER_STATE_SUCCESS." WHERE ( is_yanchi = '1' ) AND ( delay_time <= ".TIMESTAMP." ) AND ( order_state = '30' )");
			@$updates = $model_order->query("UPDATE `zmkj_order` SET  finnshed_time= `delay_time`+3600*24*7,is_yanchi = '1', order_state=".ORDER_STATE_SUCCESS." WHERE ( delay_time != '0' ) AND ( delay_time <= ".$delay_time." ) AND ( order_state = '30' )");
			
			//$condition[''] = ORDER_STATE_SUCCESS;
			//$update = $model_order->editOrder($condition,$where);
			if ($update) {
				//更新缓存
				QueueClient::push('delOrderCountCache',$condition);
			}
            echo 1;
		} catch (Exception $e) {
            echo 2;
        }
    }
}
