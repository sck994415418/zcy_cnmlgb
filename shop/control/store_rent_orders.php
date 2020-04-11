<?php
/**
 * 卖家虚拟订单管理
 *
 *
 *
 **by 好商城V3 www.haoid.cn 运营版*/


defined('InShopNC') or exit('Access Invalid!');
class store_rent_ordersControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct();
        Language::read('member_store_index');
    }

	/**
	 * 虚拟订单列表
	 *
	 */
	public function indexOp() {
        $model_rent_order = Model('rent_order');

        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
//      if ($_GET['order_sn'] != '') {
//          $condition['order_sn'] = $_GET['order_sn'];
//      }
        if ($_GET['buyer_name'] != '') {
            $condition['buyer_name'] = $_GET['buyer_name'];
        }
        $allow_state_array = array('state_new','state_pay','state_success','state_cancel','rent_goods');
        if (in_array($_GET['state_type'],$allow_state_array)) {
            $condition['order_state'] = str_replace($allow_state_array,
                    array(ORDER_STATE_NEW,ORDER_STATE_PAY,ORDER_STATE_SUCCESS,ORDER_STATE_CANCEL), $_GET['state_type']);
        } else {
            $_GET['state_type'] = 'store_order';
        }
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_date ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_date ? strtotime($_GET['query_end_date']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }

        if ($_GET['skip_off'] == 1) {
            $condition['order_state'] = array('neq',ORDER_STATE_CANCEL);
        }
	    $order_list = $model_rent_order->getOrderList($condition, 20, '*', 'rorder_id desc');
//		var_dump($order_list);exit;
        foreach ($order_list as $key => $order) {
            //显示取消订单
            $order_list[$key]['if_cancel'] = $model_rent_order->getOrderOperateState('buyer_cancel',$order);

            //追加返回买家信息
            $order_list[$key]['extend_member'] = Model('member')->getMemberInfoByID($order['buyer_id']);
        }

        Tpl::output('order_list',$order_list);
        Tpl::output('show_page',$model_rent_order->showpage());
        self::profile_menu('list',$_GET['state_type']);
		
        Tpl::showpage('store_rent_order.index');
	}
	
	/**
	 * 可租赁的商品列表
	 * 
	 */
	public function rent_goodsOp(){
		$condition = array();
		$condition['store_id'] = $_SESSION['store_id'];
//		var_dump($condition);exit;
		$model_goods_rent = Model('goods_rent');
		$goods_list = $model_goods_rent->rent_goods_list($condition);
//		echo '<pre>';
//		var_dump($goods_list);exit;
		Tpl::output('goods_list',$goods_list);
		//计算库存
//		$model_goods = Model('goods');
//		$storage_array = $model_goods->calculateStorage($goods_list);
//		Tpl::output('storage_array',$storage_array);
		Tpl::showpage('store_rent_goods.list');
	}
	
	/**
     * 选择租赁商品
     **/
    public function goods_selectOp() {
        $model_goods = Model('goods');
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['goods_name'] = array('like', '%'.$_GET['goods_name'].'%');
        $goods_list = $model_goods->getGoodsListForPromotion($condition, '*', 10, 'xianshi');

        Tpl::output('goods_list', $goods_list);
        Tpl::output('show_page', $model_goods->showpage());
        Tpl::showpage('store_rent_goods', 'null_layout');
    }
	
	/**
     * 可租赁商品添加
     **/
    public function rent_goods_addOp() {
		$goods_id = intval($_POST['goods_id']);
		$store_id = $_SESSION['store_id'];
        $rent_price = floatval($_POST['rent_price']);
		$rent_pledge = floatval($_POST['rent_pledge']);
		$rent_short_time = $_POST['rent_short_time'];
		$date = time();
		
        $model_goods = Model('goods');
        $model_goods_rent = Model('goods_rent');
		
        $data = array();
        $data['result'] = true;
		
		$condition['goods_id'] = $goods_id;
		$condition['rent_goods_state'] = 0;
		$goods_info = $model_goods_rent->field('*')->where($condition)->select();
		if($goods_info){
			$data['result'] = false;
            $data['message'] = '该商品正在租赁列表中';
            echo json_encode($data);die;
		}
		//查找商品id对应的信息 存入rent表中
		$condition1['goods_id'] = $goods_id;
		$goods_info = $model_goods->query('select * from zmkj_goods where goods_id = '.$goods_id);
		if($goods_info){
			//添加到活动商品表
	        $param = array();
	        $param['goods_id'] = $goods_id;
			$param['store_id'] = $store_id;
			$param['rent_addtime'] = $date;
			$param['rent_short_time'] = $rent_short_time;
			$param['rent_money'] = $rent_price;
			$param['cash_pledge'] = $rent_pledge;
			$param['rent_goods_state'] = 0;
			
			$param['goods_commonid'] = $goods_info[0]['goods_commonid'];
			$param['goods_name'] = $goods_info[0]['goods_name'];
			$param['brand_id'] = $goods_info[0]['brand_id'];
			$param['goods_price'] = $goods_info[0]['goods_price'];
			$param['goods_storage'] = $goods_info[0]['goods_storage'];
			$param['goods_image'] = $goods_info[0]['goods_image'];
//			
			$result = array();
			$rent_goods_info = $model_goods_rent->insert($param);
	//		print_r(log::read());exit;
	        if($rent_goods_info) {
	            $result['result'] = true;
	            $data['message'] = '添加成功';
	        } else {
	            $data['result'] = false;
	            $data['message'] = L('param_error');
	        }
	        echo json_encode($data);die;
			}
    }
	
	/**
     * 删除可租赁商品
     */
    public function drop_goodsOp() {
        $common_id = $_GET['goods_commonid'];
        $commonid_array = explode(',', $common_id);
//		var_dump($commonid_array);exit;
        $model_goods_rent = Model('goods_rent');
        $where = array();
        $where['goods_commonid'] = array('in', $commonid_array);
        $where['store_id'] = $_SESSION['store_id'];
        $return = $model_goods_rent->delet_rent_goods($where);
        if ($return) {
            // 添加操作日志
            $this->recordSellerLog('已删除选中商品');
            showDialog(L('store_goods_index_goods_del_success'), 'reload', 'succ');
        } else {
            showDialog(L('store_goods_index_goods_del_fail'), '', 'error');
        }
    }
	  
	/**
	 * 修改租赁商品信息
	 */  
	  
	  
	/**
	 * 卖家订单详情
	 *
	 */
	public function show_orderOp() {
	    $rorder_id = intval($_GET['rorder_id']);
//		var_dump($order_id);exit;
	    if ($rorder_id <= 0) {
	        showMessage(Language::get('wrong_argument'),'','html','error');
	    }
	    $model_rent_order = Model('rent_order');
	    $condition = array();
        $condition['rorder_id'] = $rorder_id;
        $condition['store_id'] = $_SESSION['store_id'];
	    $order_info = $model_rent_order->getOrderInfo($condition);
		$model_goods = Model('goods');
		$goods_name = $model_goods->query('select `goods_name` from zmkj_goods where goods_id = '.$order_info['goods_id']);
//		var_dump($order_info);exit;
		$order_info['goods_name'] = $goods_name[0]['goods_name'];
	    if (empty($order_info)) {
	        showMessage(Language::get('store_order_none_exist'),'','html','error');
	    }

        //取兑换码列表
//      $vr_code_list = $model_rent_order->getOrderCodeList(array('rorder_id' => $order_info['rorder_id']));
//      $order_info['extend_rent_order_code'] = $vr_code_list;

        //显示取消订单
        $order_info['if_cancel'] = $model_rent_order->getOrderOperateState('buyer_cancel',$order_info);

        //显示订单进行步骤
        $order_info['step_list'] = $model_rent_order->getOrderStep($order_info);

        //显示系统自动取消订单日期
        if ($order_info['order_state'] == ORDER_STATE_NEW) {
            //$order_info['order_cancel_day'] = $order_info['add_time'] + ORDER_AUTO_CANCEL_DAY * 24 * 3600;
			// by haoid.cn
			$order_info['order_cancel_day'] = $order_info['add_time'] + ORDER_AUTO_CANCEL_DAY + 3 * 24 * 3600;
        }

        Tpl::output('order_info',$order_info);

		Tpl::showpage('store_rent_order.show');
	}

	/**
	 * 卖家订单状态操作
	 *
	 */
	public function change_stateOp() {
	    $model_rent_order = Model('rent_order');
	    $condition = array();
	    $condition['rorder_id'] = intval($_GET['rorder_id']);
	    $condition['store_id'] = $_SESSION['store_id'];
	    $order_info	= $model_rent_order->getOrderInfo($condition);
	    if ($_GET['state_type'] == 'order_cancel') {
	        $result = $this->_order_cancel($order_info,$_POST);
	    }
	    if(!$result['state']) {
	        showDialog($result['msg'],'','error');
	    } else {
	        showDialog($result['msg'],'reload','js');
	    }
	}

	/**
	 * 取消订单
	 * @param arrty $order_info
	 * @param arrty $post
	 * @throws Exception
	 */
	private function _order_cancel($order_info, $post) {
	    if(!chksubmit()) {
	        Tpl::output('rorder_id',$order_info['rorder_id']);
	        Tpl::output('order_info',$order_info);
	        Tpl::showpage('store_rent_order.cancel','null_layout');
	        exit();
	    } else {
	        $model_rent_order = Model('rent_order');
	        $logic_rent_order = Logic('rent_order');
	        $if_allow = $model_rent_order->getOrderOperateState('store_cancel',$order_info);
            if (!$if_allow) {
                return callback(false,'无权操作');
            }
            $msg = $post['state_info1'] != '' ? $post['state_info1'] : $post['state_info'];
            return $logic_rent_order->changeOrderStateCancel($order_info,'seller', $msg);
	    }
	}

	public function exchangeOp() {
	    $data = $this->_exchange();
	    exit(json_encode($data));
	}

	/**
	 * 兑换码消费
	 */
    private function _exchange() {
        if (chksubmit()) {
            if (!preg_match('/^[a-zA-Z0-9]{15,18}$/',$_GET['vr_code'])) {
                return array('error' => '兑换码格式错误，请重新输入');
            }
            $model_rent_order = Model('rent_order');
            $vr_code_info = $model_rent_order->getOrderCodeInfo(array('vr_code' => $_GET['vr_code']));
            if (empty($vr_code_info) || $vr_code_info['store_id'] != $_SESSION['store_id']) {
                return array('error' => '该兑换码不存在');
            }
            if ($vr_code_info['vr_state'] == '1') {
                return array('error' => '该兑换码已被使用');
            }
            if ($vr_code_info['vr_indate'] < TIMESTAMP) {
                return array('error' => '该兑换码已过期，使用截止日期为： '.date('Y-m-d H:i:s',$vr_code_info['vr_indate']));
            }
            if ($vr_code_info['refund_lock'] > 0) {//退款锁定状态:0为正常,1为锁定(待审核),2为同意
                return array('error' => '该兑换码已申请退款，不能使用');
            }

            //更新兑换码状态
            $update = array();
            $update['vr_state'] = 1;
            $update['vr_usetime'] = TIMESTAMP;
            $update = $model_rent_order->editOrderCode($update, array('vr_code' => $_GET['vr_code']));

            //如果全部兑换完成，更新订单状态
            Logic('rent_order')->changeOrderStateSuccess($vr_code_info['order_id']);

            if ($update) {
                //取得返回信息
                $order_info = $model_rent_order->getOrderInfo(array('order_id'=>$vr_code_info['order_id']));
                if ($order_info['use_state'] == '0') {
                    $model_rent_order->editOrder(array('use_state' => 1), array('order_id' => $vr_code_info['order_id']));
                }
                $order_info['img_60'] = thumb($order_info,60);
                $order_info['img_240'] = thumb($order_info,240);
                $order_info['goods_url'] = urlShop('goods','index',array('goods_id' => $order_info['goods_id']));
                $order_info['order_url'] = urlShop('store_rent_order','show_order',array('order_id' => $order_info['order_id']));
                return array('error'=>'', 'data' => $order_info);
            }

        } else {
            self::profile_menu('exchange','exchange');
            Tpl::showpage('store_rent_order.exchange');
        }
    }

	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return
     */
    private function profile_menu($menu_type='',$menu_key='') {
        Language::read('member_layout');
        switch ($menu_type) {
        	case 'list':
            $menu_array = array(
            array('menu_key'=>'store_order',		'menu_name'=>Language::get('nc_member_path_all_order'),	'menu_url'=>'index.php?act=store_rent_order'),
            //array('menu_key'=>'rent_goods',			'menu_name'=>'商品列表',	'menu_url'=>'index.php?act=store_rent_order&op=rent_goods&state_type=rent_goods'),
            array('menu_key'=>'state_new',			'menu_name'=>Language::get('nc_member_path_wait_pay'),	'menu_url'=>'index.php?act=store_rent_order&op=index&state_type=state_new'),
            array('menu_key'=>'state_pay',			'menu_name'=>'已付款',	'menu_url'=>'index.php?act=store_rent_order&op=index&state_type=state_pay'),
            array('menu_key'=>'state_success',		'menu_name'=>Language::get('nc_member_path_finished'),	'menu_url'=>'index.php?act=store_rent_order&op=index&state_type=state_success'),
            array('menu_key'=>'state_cancel',		'menu_name'=>Language::get('nc_member_path_canceled'),	'menu_url'=>'index.php?act=store_rent_order&op=index&state_type=state_cancel'),
            );
            break;
            case 'exchange':
                $menu_array = array(
                array('menu_key'=>'store_order',		'menu_name'=>Language::get('nc_member_path_all_order'),	'menu_url'=>'index.php?act=store_rent_order'),
                array('menu_key'=>'state_new',			'menu_name'=>Language::get('nc_member_path_wait_pay'),	'menu_url'=>'index.php?act=store_rent_order&op=index&state_type=state_new'),
                array('menu_key'=>'state_pay',			'menu_name'=>'已付款',	'menu_url'=>'index.php?act=store_rent_order&op=index&state_type=state_pay'),
                array('menu_key'=>'state_success',		'menu_name'=>Language::get('nc_member_path_finished'),	'menu_url'=>'index.php?act=store_rent_order&op=index&state_type=state_success'),
                array('menu_key'=>'state_cancel',		'menu_name'=>Language::get('nc_member_path_canceled'),	'menu_url'=>'index.php?act=store_rent_order&op=index&state_type=state_cancel'),
                array('menu_key'=>'exchange',		    'menu_name'=>'兑换码兑换',	'menu_url'=>'index.php?act=store_rent_order&op=exchange'),
                );
                break;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
