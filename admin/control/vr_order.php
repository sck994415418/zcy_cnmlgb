<?php
/**
 * 虚拟订单管理 v3-b12
 *
 *
 *
 **by 好商城V3 www.haoid.cn 运营版*/

defined('InShopNC') or exit('Access Invalid!');
class vr_orderControl extends SystemControl{
    /**
     * 每次导出订单数量
     * @var int
     */
	const EXPORT_SIZE = 1000;

	public function __construct(){
		parent::__construct();
		Language::read('trade');
	}

	public function wx_stateOp(){
		$refund_fee = isset($_GET['refund_amount'])?$_GET['refund_amount']:'';
		$trade_no = isset($_GET['trade_no'])?$_GET['trade_no']:'';
		$map['refund_amount'] = $refund_fee;
		$map['paystate'] = 1;
		$where['trade_no'] = $trade_no;
		Model()->table('order')->where($where)->update($map);

		showMessage('退款成功！','index.php?act=vr_order&op=index');
	}
	
	public function allinpay_stateOp(){
		$refund_fee = isset($_GET['refund_amount'])?$_GET['refund_amount']:'';
		$trade_no = isset($_GET['trade_no'])?$_GET['trade_no']:'';
		$map['refund_amount'] = $refund_fee;
		$map['paystate'] = 1;
		$where['trade_no'] = $trade_no;
		Model()->table('order')->where($where)->update($map);

		showMessage('退款成功！','index.php?act=refund&op=refund_all');
	}

	public function indexOp(){
	    $model_vr_order = Model('order');
        $condition	= array();
        $condition['is_master'] = '1';
        if($_GET['order_sn']) {
        	$condition['order_sn'] = $_GET['order_sn'];
        }
        if($_GET['store_name']) {
            $condition['store_name'] = $_GET['store_name'];
        }
        if(!empty($_GET['order_state'])){
        	$condition['order_state'] = intval($_GET['order_state']);
        }
        if($_GET['payment_code']) {
            $condition['payment_code'] = $_GET['payment_code'];
        }
        if($_GET['buyer_name']) {
            $condition['buyer_name'] = $_GET['buyer_name'];
        }
        $if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_time']);
        $if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_time']);
        $start_unixtime = $if_start_time ? strtotime($_GET['query_start_time']) : null;
        $end_unixtime = $if_end_time ? strtotime($_GET['query_end_time']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }
       	$smcost = Model()->table("setting")->field("value")->where("name='smcost'")->find();
        $order_list	= $model_vr_order->getOrderList($condition,30);

        foreach ($order_list as $k => $order_info) {
        	$order_list[$k]['cost'] = floatval($order_info['scost'])+floatval($smcost['value']);
        	$master = $order_info['master_id'];
        	$map['member_id'] = $master;
        	$masters = Model()->table("member")->field("member_name")->where($map)->find();
        	$order_list[$k]['master'] = $masters['member_name'];
        	if($order_info['wxstate'] == 0){
        		$order_list[$k]['state_desc'] = '下单';
        	}
        	if($order_info['wxstate'] == 1){
        		$order_list[$k]['state_desc'] = '已接单';
        	}
        	if($order_info['wxstate'] == 2){
        		$order_list[$k]['state_desc'] = '出发';
        	}
        	if($order_info['wxstate'] == 3){
        		$order_list[$k]['state_desc'] = '维修中';
        	}
        	if($order_info['wxstate'] == 4){
        		$order_list[$k]['state_desc'] = '完成';
        	}
        	if($order_info['wxstate'] == 5){
        		$order_list[$k]['state_desc'] = '已取消';
        	}
        	if($order_info['paytype'] == 'zfb'){
				$order_list[$k]['pay']['WIDbatch_no']    = date("Ymd",time()).'00'.$order_info['order_id'];
	        	$order_list[$k]['pay']['WIDbatch_num']   = 1;
	        	$order_list[$k]['pay']['WIDdetail_data'] = $order_info['trade_no'].'^'.$order_info['order_amount'].'^'.'维修订单退款';
        	}
        	if($order_info['paytype'] == 'wx'){
        		$order_list[$k]['pay']['refund_fee']  = $order_info['order_amount']*100;
	        	$order_list[$k]['pay']['total_fee']   = $order_info['order_amount']*100;
	        	$order_list[$k]['pay']['transaction_id'] = $order_info['trade_no'];
        	}
        	
        }



        //显示支付接口列表(搜索)
        $payment_list = Model('payment')->getPaymentOpenList();
        Tpl::output('payment_list',$payment_list);

        Tpl::output('order_list',$order_list);
        Tpl::output('show_page',$model_vr_order->showpage());
        Tpl::showpage('vr_order.index');
	}

	/**
	 * 平台订单状态操作
	 *
	 */
	public function refunt_moneyOp(){

	}

	/**
	 * 平台订单状态操作
	 *
	 */
	public function change_stateOp() {
	    $model_order = Model('order');
	    $condition = array();
	    $where['order_id'] = intval($_GET['order_id']);
	    $condition['wxstate'] = intval(5);
	    $result	= $model_order->table("order")->where($where)->update($condition);
	    if(!$result) {
	        showMessage($result['msg'],$_POST['ref_url'],'html','error');
	    } else {
	        showMessage($result['msg'],$_POST['ref_url']);
	    }
	}

	/**
	 * 系统取消订单
	 * @param unknown $order_info
	 */
	private function _order_cancel($order_info) {
	    $model_vr_order = Model('order');
	    $logic_vr_order = Logic('order');
	    $if_allow = $model_vr_order->getOrderOperateState('system_cancel',$order_info);
	    if (!$if_allow) {
	        return callback(false,'无权操作');
	    }
	    $this->log('关闭了虚拟订单,'.L('order_number').':'.$order_info['order_sn'],1);
	    return $logic_vr_order->changeOrderStateCancel($order_info,'store', '管理员关闭虚拟订单');
	}

	/**
	 * 系统收到货款
	 * @param unknown $order_info
	 * @throws Exception
	 */
	private function _order_receive_pay($order_info,$post) {
	    $model_vr_order = Model('order');
	    $logic_vr_order = Logic('order');
	    $if_allow = $model_vr_order->getOrderOperateState('system_receive_pay',$order_info);
	    if (!$if_allow) {
	        return callback(false,'无权操作');
	    }

	    if (!chksubmit()) {
	        Tpl::output('order_info',$order_info);
	        //显示支付接口
	        $payment_list = Model('payment')->getPaymentOpenList();
	        //去掉预存款和货到付款
	        foreach ($payment_list as $key => $value){
	            if ($value['payment_code'] == 'predeposit' || $value['payment_code'] == 'offline') {
	               unset($payment_list[$key]);
	            }
	        }
	        Tpl::output('payment_list',$payment_list);
	        Tpl::showpage('order.receive_pay');
	        exit();
	    } else {
	        $this->log('将虚拟订单改为已收款状态,'.L('order_number').':'.$order_info['order_sn'],1);
	        return $logic_vr_order->changeOrderStatePay($order_info,'system', $post);
	    }
	}

	/**
	 * 查看订单
	 *
	 */
	public function show_orderOp(){
		if ($_POST['master_id'] != '') {
			$order_id  = intval($_POST['order_id']);
			$master_id = intval($_POST['master_id']);
			if($master_id == ''){
				showMessage('师傅','','html','error');
			}
			$memap['member_id'] = $master_id;
			$master = Model()->table("member")->where($memap)->find();
			$phone = $master['member_mobile'];
			$order_sn = $_POST['order_sn'];
			$model_order = Model('order');
		    $condition = array();
		    $where['order_id'] = $order_id;
		    $condition['wxstate'] = 1;
		    $condition['master_id'] = $master_id;
		    $condition['jdtime'] = time();
		    $result	= $model_order->table("order")->where($where)->update($condition);
		    if(!$result) {
		        showMessage($result['msg'],$_POST['ref_url'],'html','error');
		    } else {
		    	$cond['message_body'] = "维修师傅你好,系统已经为您指派订单：".$order_sn.",请您尽快处理！";
		    	$sms = new Sms();
                $result = $sms->send($phone,$cond['message_body']);
		    	$cond['to_member_id'] = $master_id;
		    	$cond['message_time'] = time();
		    	$cond['message_type'] = 1;
		    	Model()->table("message")->insert($cond);
		        showMessage($result['msg'],$_POST['ref_url']);
		    }
		}
	    $order_id = intval($_GET['order_id']);
	    if($order_id <= 0 ){
	        showMessage(L('miss_order_number'));
	    }
        $model_order	= Model('order');
        $order_info	= $model_order->getOrderInfo(array('order_id'=>$order_id),array('order_goods','order_common','store'));
        if (empty($order_info)) {
            showMessage('订单不存在','','html','error');
        }
        $smcost = Model()->table("setting")->field("value")->where("name='smcost'")->find();
    	$order_info['cost'] = floatval($order_info['scost'])+floatval($smcost['value']);
    	$master = $order_info['master_id'];
    	$map['member_id'] = $master;
    	$masters = Model()->table("member")->field("member_name")->where($map)->find();
    	$order_info['master'] = $masters['member_name'];
    	if($order_info['wxstate'] == 0){
    		$order_info['state_desc'] = '下单';
    	}
    	if($order_info['wxstate'] == 1){
    		$order_info['state_desc'] = '接单';
    	}
    	if($order_info['wxstate'] == 2){
    		$order_info['state_desc'] = '出发';
    	}
    	if($order_info['wxstate'] == 3){
    		$order_info['state_desc'] = '维修中';
    	}
    	if($order_info['wxstate'] == 4){
    		$order_info['state_desc'] = '完成';
    	}
    	if($order_info['wxstate'] == 5){
    		$order_info['state_desc'] = '已取消';
    	} 
    	$emap['geval_orderid'] = $order_id;
    	$evaluate = Model()->table("evaluate_goods")->where($emap)->find();
    	$order_info['evaluate_content'] = $evaluate['geval_content'];
    	$order_info['evaluate_star']    = $evaluate['geval_scores'];
    	$order_info['evaluate_addtime'] = $evaluate['geval_addtime'];
    	$condition['is_master'] = 1;
    	$model_member = Model("member");
    	// $member = $model_member->table("member")->where($condition)->select();
    	$member = $model_member->getMemberList($condition, '*', '', '');

		Tpl::output('member',$member);
		Tpl::output('order_info',$order_info);
        Tpl::showpage('vr_order.view');
	}

	/**
	 * 导出
	 *
	 */
	public function export_step1Op(){
		$lang	= Language::getLangContent();

	    $model_vr_order = Model('order');
        $condition	= array();
        if($_GET['order_sn']) {
        	$condition['order_sn'] = $_GET['order_sn'];
        }
        if($_GET['store_name']) {
            $condition['store_name'] = $_GET['store_name'];
        }
        if(in_array($_GET['order_state'],array('0','10','20','30','40'))){
        	$condition['order_state'] = $_GET['order_state'];
        }
        if($_GET['payment_code']) {
            $condition['payment_code'] = $_GET['payment_code'];
        }
        if($_GET['buyer_name']) {
            $condition['buyer_name'] = $_GET['buyer_name'];
        }
        $condition['is_master'] = array('neq',1);
        $if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_time']);
        $if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_time']);
        $start_unixtime = $if_start_time ? strtotime($_GET['query_start_time']) : null;
        $end_unixtime = $if_end_time ? strtotime($_GET['query_end_time']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }

		if (!is_numeric($_GET['curpage'])){
			$count = $model_vr_order->getOrderCount($condition);
			$array = array();
			if ($count > self::EXPORT_SIZE ){	//显示下载链接
				$page = ceil($count/self::EXPORT_SIZE);
				for ($i=1;$i<=$page;$i++){
					$limit1 = ($i-1)*self::EXPORT_SIZE + 1;
					$limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
					$array[$i] = $limit1.' ~ '.$limit2 ;
				}
				Tpl::output('list',$array);
				Tpl::output('murl','index.php?act=vr_order&op=index');
				Tpl::showpage('export.excel');
			}else{	//如果数量小，直接下载
				$data = $model_vr_order->getOrderList($condition,'','*','order_id desc',self::EXPORT_SIZE);
				$this->createExcel($data);
			}
		}else{	//下载
			$limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
			$limit2 = self::EXPORT_SIZE;
			$data = $model_vr_order->getOrderList($condition,'','*','order_id desc',"{$limit1},{$limit2}");
			$this->createExcel($data);
		}
	}

	/**
	 * 生成excel
	 *
	 * @param array $data
	 */
	private function createExcel($data = array()){
		Language::read('export');
		import('libraries.excel');
		$excel_obj = new Excel();
		$excel_data = array();
		//设置样式
		$excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
		//header
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_no'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_store'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_buyer'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_xtimd'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_count'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_paytype'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_state'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_storeid'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_buyerid'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>'接收手机');
		//data
		foreach ((array)$data as $k=>$v){
			$tmp = array();
			$tmp[] = array('data'=>'NC'.$v['order_sn']);
			$tmp[] = array('data'=>$v['store_name']);
			$tmp[] = array('data'=>$v['buyer_name']);
			$tmp[] = array('data'=>date('Y-m-d H:i:s',$v['add_time']));
			$tmp[] = array('format'=>'Number','data'=>ncPriceFormat($v['order_amount']));
			$tmp[] = array('data'=>orderPaymentName($v['payment_code']));
			$tmp[] = array('data'=>$v['state_desc']);
			$tmp[] = array('data'=>$v['store_id']);
			$tmp[] = array('data'=>$v['buyer_id']);
			$tmp[] = array('data'=>$v['buyer_phone']);
			$excel_data[] = $tmp;
		}
		$excel_data = $excel_obj->charset($excel_data,CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
		$excel_obj->generateXML($excel_obj->charset(L('exp_od_order'),CHARSET).$_GET['curpage'].'-'.date('Y-m-d-H',time()));
	}
}
