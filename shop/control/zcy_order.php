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
            case 'zcy_order_list':// 商品批量改价
                Tpl::showpage('store_goods_list.change_price_change_all');
                break;
            case 'change_yingshe':// 主动映射的商品改价
                Tpl::showpage('store_goods_yingshe.change_price');
                break;
			case 'update_productid':// 主动映射的商品改价
                Tpl::showpage('store_goods.update_productid');
                break;
            default://使用模版zcy_order_list.php
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
        Tpl::output ( 'member_menu', $menu_array );
        Tpl::output ( 'menu_key', $menu_key );
    }
    /**
     * 政采云买家订单详情
     *
     */
    public function show_orderOp() {

        Language::read('member_member_index');
        $orderId = array($_GET['orderId']);
        $status = $_GET['status'];
        require_once(BASE_PATH.'/../zcy/nr_zcy.php');
        $zcy = new nr_zcy();
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

}
?>