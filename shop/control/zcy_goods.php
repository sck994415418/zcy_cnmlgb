<?php
/**
 * 政采云平台商品管理 v3-b12
 *
 */
defined('InShopNC') or exit('Access Invalid!');
class zcy_goodsControl extends BaseSellerControl {
	//需要对接政采云的店铺store_id
	private $zcy_store = array(51,61,1);
    private $nrzcy = null;
    public function __construct() {
        parent::__construct();
        Language::read('member_store_goods_index');
		//验证是否有政采云操作权限
		if(! in_array($_SESSION["store_id"] , $this->zcy_store)){
			exit("当前店铺没有此权限！请<a href=\"/shop/index.php?act=seller_center&op=index\">返回</a>");
		}

    }

    public function indexOp() {
        require_once(BASE_PATH.'/../zcy/nr_zcy.php');
        $zcy = $this->nrzcy = new nr_zcy("314930527","rCT3MqDWnuSvYUhQfkzN");
        $zcy = $zcy->get_category(0,4);
        var_dump(json_decode($zcy));die;
        $this->zcy_goods_listOp();
    }
	
	/*
	 *政采云平台商品列表
	 *
	 */	
    public function zcy_goods_listOp() {

        switch ($_GET['type']) {
            case 'on_shelf':
                $this->profile_menu('zcy_goods_onshelf');
                break;
            case 'off_shelf':
                $this->profile_menu('zcy_goods_offshelf');
                break;
			case 'freez':
                $this->profile_menu('zcy_goods_freez');
                break;
			case 'verify':
                $this->profile_menu('zcy_goods_verify');
                break;
			case 'refuse':
                $this->profile_menu('zcy_goods_refuse');
                break;
            default:
                $this->profile_menu('zcy_goods_onshelf');
                break;
        }

        switch ($_GET['type']) {
            case 'zcy_goods_list':// 商品批量改价
                Tpl::showpage('store_goods_list.change_price_change_all');
                break;
            case 'change_yingshe':// 主动映射的商品改价
                Tpl::showpage('store_goods_yingshe.change_price');
                break;
			case 'update_productid':// 主动映射的商品改价
                Tpl::showpage('store_goods.update_productid');
                break;
            default://使用模版zcy_goods_list.php
                Tpl::showpage('zcy_goods_list');
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
            array('menu_key' => 'zcy_goods_onshelf', 'menu_name' => "已上架商品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'on_shelf'))),
			array('menu_key' => 'zcy_goods_verify', 'menu_name' => "待审核商品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'verify'))),
            array('menu_key' => 'zcy_goods_offshelf', 'menu_name' => "已下架商品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'off_shelf'))),
            array('menu_key' => 'zcy_goods_freez', 'menu_name' => "已冻结商品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'freez'))),
			array('menu_key' => 'zcy_goods_refuse', 'menu_name' => "审核不通过商品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'refuse')))
        );
        Tpl::output ( 'member_menu', $menu_array );
        Tpl::output ( 'menu_key', $menu_key );
    }
	//添加商品
    public function add_goods()
    {

    }
    // 编辑商品
    public function edit_goods()
    {

    }
    //删除商品
    public function del_goods()
    {

    }
	
}
?>