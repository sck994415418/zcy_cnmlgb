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
			$where['is_yanchi'] = 1;
			$where['delay_time'] = array('elt', TIMESTAMP);
			$where['order_state'] = 30;
			$condition['finnshed_time'] = TIMESTAMP;
			@$update = $model_order->query("UPDATE `zmkj_order` SET finnshed_time= `delay_time`, order_state=".ORDER_STATE_SUCCESS." WHERE ( is_yanchi = '1' ) AND ( delay_time <= ".TIMESTAMP." ) AND ( order_state = '30' )");
			
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
