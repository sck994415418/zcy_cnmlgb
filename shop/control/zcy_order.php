<?php
/**
 * 政采云平台订单管理 v3-b12
 *
 */
defined('InShopNC') or exit('Access Invalid!');
class zcy_orderControl extends BaseSellerControl {
	//需要对接政采云的店铺store_id
	private $zcy_store = array(51,61,1);
    private $nrzcy = null;
    public function __construct() {
        parent::__construct();
        Language::read('member_store_index');
		//验证是否有政采云操作权限
		if(! in_array($_SESSION["store_id"] , $this->zcy_store)){
			exit("当前店铺没有此权限！请<a href=\"/shop/index.php?act=seller_center&op=index\">返回</a>");
		}

    }

    public function indexOp() {
//        require_once(BASE_PATH.'/../zcy/nr_zcy.php');
//        $zcy = $this->nrzcy = new nr_zcy("314930527","rCT3MqDWnuSvYUhQfkzN");
//        $zcy = $zcy->get_category(0,4);
//        var_dump(json_decode($zcy));die;
        include_once 'zcy_common.php';
        $res = new zcy_commonControl();
        $res->aa();
        $this->zcy_order_listOp();
    }
	
	/*
	 *政采云平台订单列表
	 *
	 */	
    public function zcy_order_listOp() {

        switch ($_GET['status']) {
            case '0'://待接单
                $this->profile_menu('0');
                break;
            case '1'://已接单待发货
                $this->profile_menu('1');
                break;
			case '2'://已部分发货待确认
                $this->profile_menu('2');
                break;
			case '3'://全部发货,待确认收货
                $this->profile_menu('3');
                break;
			case '4'://已确认收货,待验收
                $this->profile_menu('4');
                break;
            case '5'://已验收待结算
                $this->profile_menu('5');
                break;
            case '6'://启动结算
                $this->profile_menu('6');
                break;
            case '7'://交易完成
                $this->profile_menu('7');
                break;
            case '-4'://采购人申请取消订单
                $this->profile_menu('-4');
                break;
            case '10'://退换货中
                $this->profile_menu('10');
                break;
            case '-2'://供应商拒绝接单
                $this->profile_menu('-2');
                break;
            case '-5'://供应商同意取消订单
                $this->profile_menu('-5');
                break;
            case '-6'://全部退货、订单关闭
                $this->profile_menu('-6');
                break;
            default:
                $this->profile_menu('0');
                break;
        }

        switch ($_GET['type']) {
            case 'zcy_order'://订单列表
                Tpl::showpage('zcy_order_list');
                break;
            case 'send_order':// 主动映射的商品改价
                Tpl::showpage('zcy_send_order');
                break;
			case 'is_agree_order':// 主动映射的商品改价
                Tpl::showpage('zcy_is_agree_order');
                break;
            case 'order_other_info':// 主动映射的商品改价
                Tpl::showpage('zcy_order_other_info');
                break;
            default://订单列表
                Tpl::showpage('zcy_order_list');
                break;
        }
    }

	
    /**
     * 用户中心右边，小导航
     *
     * @param string $menu_key 当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_key = '') {
        $menu_array = array(
            array('menu_key' => '0', 'menu_name' => "待接单", 'menu_url' => urlShop('zcy_order', 'index', array('status' => '0','type'=>'list'))),
			array('menu_key' => '1', 'menu_name' => "已接单待发货", 'menu_url' => urlShop('zcy_order', 'index', array('status' => '1','type'=>'list'))),
            array('menu_key' => '2', 'menu_name' => "已部分发货待确认", 'menu_url' => urlShop('zcy_order', 'index', array('status' => '2','type'=>'list'))),
            array('menu_key' => '3', 'menu_name' => "全部发货,待确认收货", 'menu_url' => urlShop('zcy_order', 'index', array('status' => '3','type'=>'list'))),
			array('menu_key' => '4', 'menu_name' => "已确认收货,待验收", 'menu_url' => urlShop('zcy_order', 'index', array('status' => '4','type'=>'list'))),
			array('menu_key' => '5', 'menu_name' => "已验收待结算", 'menu_url' => urlShop('zcy_order', 'index', array('status' => '5','type'=>'list'))),
			array('menu_key' => '6', 'menu_name' => "启动结算", 'menu_url' => urlShop('zcy_order', 'index', array('status' => '6','type'=>'list'))),
			array('menu_key' => '7', 'menu_name' => "交易完成", 'menu_url' => urlShop('zcy_order', 'index', array('status' => '7','type'=>'list'))),
			array('menu_key' => '-4', 'menu_name' => "采购人申请取消订单", 'menu_url' => urlShop('zcy_order', 'index', array('status' => '-4','type'=>'list'))),
			array('menu_key' => '10', 'menu_name' => "退换货中", 'menu_url' => urlShop('zcy_order', 'index', array('status' => '10','type'=>'list'))),
			array('menu_key' => '-2', 'menu_name' => "供应商拒绝接单", 'menu_url' => urlShop('zcy_order', 'index', array('status' => '-2','type'=>'list'))),
			array('menu_key' => '-5', 'menu_name' => "供应商同意取消订单", 'menu_url' => urlShop('zcy_order', 'index', array('status' => '-5','type'=>'list'))),
			array('menu_key' => '-6', 'menu_name' => "全部退货、订单关闭", 'menu_url' => urlShop('zcy_order', 'index', array('status' => '-6','type'=>'list'))),
        );
        Tpl::output ('member_menu', $menu_array );
        Tpl::output ('menu_key', $menu_key );
    }
    /**
     * 政采云买家订单详情
     *
     */
    public function show_orderOp() {

        $orderId = array($_GET['orderId']);
        $status = $_GET['status'];
        require_once(BASE_PATH.'/../zcy/nr_zcy.php');
        $zcy = new nr_zcy();
//        $orderId = 1509792000002605151;
//        $res = $zcy->take_order($orderId);
//        echo '<pre>';
//        print_r($res);
//        die;



        $rs = $zcy->order_list($status,$orderId,1,1);
//        echo '<pre>';
//        print_r($rs);
//        exit();
        if($rs['success'] == 1 && $rs['data_response']['total']>=1){
            Tpl::output('order_details',$rs['data_response']['data'][0]);
            Tpl::showpage('zcy_order.show');
        }else{
            echo '订单不存在！';
            die;
        }
    }



    /**
     * 接受订单
     *
     */
    public function take_orderOp() {
        $orderId = array($_GET['orderId']);
        require_once(BASE_PATH.'/../zcy/nr_zcy.php');
        $zcy = new nr_zcy();
        $rs = $zcy->take_order($orderId);
//        var_dump($rs);die;
        if ($rs['success'] == true) {
            // 添加操作日志
            $this->recordSellerLog('订单接单，政采云订单ID：' . $orderId);
            showDialog(L('store_goods_index_goods_del_success'), 'reload', 'succ');
        } else {
            showDialog(L('store_goods_index_goods_del_fail'), '', 'error');
        }
        return $rs;
    }
    /**
     * 拒绝订单
     */
    public function refuse_orderOp()
    {
        $orderId = array($_GET['orderId']);
        require_once(BASE_PATH.'/../zcy/nr_zcy.php');
        $zcy = new nr_zcy();
        $rs = $zcy->refuse_order($orderId);
        if ($rs['success'] == true) {
            // 添加操作日志
            $this->recordSellerLog('订单拒单，政采云订单ID：' . $orderId);
            showDialog(L('store_goods_index_goods_del_success'), 'reload', 'succ');
        } else {
            showDialog(L('store_goods_index_goods_del_fail'), '', 'error');
        }
        return $rs;
    }

    /**
     * 订单发货
     * @return bool|mixed|string|nulld
     */
    public function send_orderOp()
    {
        $order_info = $_POST;
//        echo '<pre>';
//        print_r($order_info);die;
        if(empty($order_info['skus']['quantity'])){
            $res['code'] = -1;
            $res['msg'] = '请输入发货数量';
            $res = json_encode($res);
            exit($res);
        }elseif(empty($order_info['shipmentNo'])){
            $res['code'] = -1;
            $res['msg'] = '请输入物流单号';
            $res = json_encode($res);
            exit($res);
        }

        if($order_info['expressCode'] == 1){
            if(empty($order_info['expressCode'])){
                $res['code'] = -1;
                $res['msg'] = '发货方式为物流发货时，发货物流公司代码不能为空';
                $res = json_encode($res);
                exit($res);
            }
        }
        $skus = $order_info['skus'];
        $shipmentType = $order_info['shipmentType'];
        $shipmentNo = $order_info['shipmentNo'];
        $expressCode = $order_info['expressCode'];
        $orderId = $order_info['orderId'];
        require_once(BASE_PATH.'/../zcy/nr_zcy.php');
        $zcy = new nr_zcy();
        $rs = $zcy->send_order($skus,$shipmentType,$shipmentNo,$expressCode,$orderId);
        if($rs['success']==true){
            $res['code'] =1;
            $res['msg'] = $rs['error_response']['resultMsg'];
        }else{
            $res['code'] =-1;
            $res['msg'] = $rs['error_response']['resultMsg'];
        }
        $res = json_encode($res);
        exit($res);
//        var_dump($order_info);
//        echo '<pre>';
//        print_r($rs);
//        die;
//        return $rs;
    }

    /**
     * 同意退换货
     */
    public function agree_returnOp()
    {
        $return_info = $_POST;
        $checkComment = $return_info['checkComment'];
        $pickupBeginTime = $return_info['pickupBeginTime'];
        $pickupEndTime = $return_info['pickupEndTime'];
        $returnOrderId = $return_info['returnOrderId'];
        $addressId = $return_info['addressId'];
        $address = $return_info['address'];
        $mobile = $return_info['mobile'];
        $receiverName = $return_info['receiverName'];
        require_once(BASE_PATH.'/../zcy/nr_zcy.php');
        $zcy = new nr_zcy();
        $rs = $zcy->send_order($checkComment,$pickupBeginTime,$pickupEndTime,$returnOrderId,$addressId,$address,$mobile,$receiverName);
        return $rs;
    }


    /**
     * 同意|拒绝取消订单
     * @return bool|mixed|string|nulld
     */
    public function is_agree_orderOp()
    {
        $order_info = $_POST;
        $orderId = $order_info['orderId'];
        $isAgree = $order_info['isAgree'];
        $comment = $order_info['comment'];
        require_once(BASE_PATH.'/../zcy/nr_zcy.php');
        $zcy = new nr_zcy();
        $rs = $zcy->is_agree_order($orderId,$isAgree,$comment);
        if($rs['success']==true){
            $res['code'] =1;
            $res['msg'] = $rs['error_response']['resultMsg'];
        }else{
            $res['code'] =-1;
            $res['msg'] = $rs['error_response']['resultMsg'];
        }
        $res = json_encode($res);
        exit($res);
//        var_dump($rs);
//        die;
//        return $rs;
    }
}
?>