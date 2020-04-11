<?php


//模板制作测试页

defined('InShopNC') or exit('Access Invalid!');
class aaControl extends BaseHomeControl{

    public function __construct() {
        parent::__construct();
        Tpl::output('index_sign','设备租赁');
    }
	
	/**
	 * 租赁首页/商品列表
	 */
	public function indexOp(){
		$model_rent = Model('goods_rent');
		$condition['rent_goods_state'] = 0;
		$condition['goods_state'] = 1;
		$rent_goods_list = $model_rent->field('*')->where($condition)->order('rent_addtime desc')->page(24)->select();
//		var_dump($rent_goods_list);exit;
		Tpl::output('goods_list',$rent_goods_list);
		Tpl::output('show_page', $model_rent->showpage(5));
		Tpl::showpage('rent_index');
	}
    public function indexxOp() {
		$temp = $_REQUEST['te'];//zlremenpinpai
//		var_dump($_POST);exit;
        $action = isset($_POST['action'])?$_POST['action']:'';
        if($action == 'sbwx'){
        }
        if($temp == 'shebeiweixiu'){
            $smcost = Model()->table("setting")->where("name='smcost'")->find();
            $dj = $smcost['value'];//0.01
            Tpl::output('scost', $dj);
        }
        if($temp == 'zlremenpinpai'){
        	$p = isset($_GET['p'])?$_GET['p']:1;//1
        	$start = ($p-1)*42;
        	$str = $start.",42";//0,42
            $map['brand_recommend'] = 1;
            $map['brand_apply'] = 1;
            $zong = Model()->table("brand")->where($map)->select();//找出全部申请通过并且推荐显示的品牌
//			var_dump($zong);exit;
            $brand = Model()->table("brand")->where($map)->limit($str)->select();//找出申请通过并且推荐的42个品牌
//			var_dump($brand);exit;
            $page = ceil(count($zong)/42);
            foreach ($brand as $key => $value) {
            	//找出对应的品牌logo
                if($value['brand_pic'] != ''){
                    $brand[$key]['brand_pic'] = '../data/upload/shop/brand/'.$value['brand_pic'];
                }
            }
            Tpl::output('brand', $brand);
            Tpl::output('page', $page);
            Tpl::output('p', $p);
        }
		Tpl::output('nav_link_list', $nav_link);
        Tpl::showpage($temp);
    }

    public function zlremenpinpaiOp(){
        Language::read('home_brand_index');
        $lang   = Language::getLangContent();
        /**
         * 验证品牌
         */
        $model_brand = Model('brand');
		//取单个品牌全部信息
//		var_dump($_GET['brand']);exit;
        $brand_info = $model_brand->getBrandInfo(array('brand_id' => intval($_GET['brand'])));
		var_dump($brand_info);exit;
        if(!$brand_info){
            showMessage($lang['wrong_argument'],'index.php','html','error');
        }

        /**
         * 获得推荐品牌
         */
        $brand_r_list = Model('brand')->getBrandPassedList(array('brand_recommend'=>1) ,'brand_id,brand_name,brand_pic', 0, 'brand_sort asc, brand_id desc', 10);
//		echo '<pre>';
//		var_dump($brand_r_list);exit;
        Tpl::output('brand_r',$brand_r_list);//左侧列表的推荐品牌

        // 得到排序方式
        $order = 'is_own_shop desc,goods_id desc';
        if (!empty($_GET['key'])) {
            $order_tmp = trim($_GET['key']);//移除字符串两边的空白
            $sequence = $_GET['order'] == 1 ? 'asc' : 'desc';
            switch ($order_tmp) {
                case '1' : // 销量
                    $order = 'goods_salenum' . ' ' . $sequence;
                    break;
                case '2' : // 浏览量
                    $order = 'goods_click' . ' ' . $sequence;
                    break;
                case '3' : // 价格
                    $order = 'goods_promotion_price' . ' ' . $sequence;
                    break;
            }
        }

        // 字段
        $fieldstr = "goods_id,goods_commonid,goods_name,goods_jingle,store_id,store_name,goods_price,goods_promotion_price,goods_promotion_type,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_good_star,evaluation_count,is_virtual,is_fcode,is_appoint,is_presell,have_gift";
        // 条件
        $where = array();
        $where['brand_id'] = $brand_info['brand_id'];//商品id
        if (intval($_GET['area_id']) > 0) {
            $where['areaid_1'] = intval($_GET['area_id']);//一级地区
        }
        if ($_GET['type'] == 1) {
            $where['is_own_shop'] = 1;//是否为平台自营
        }
        if ($_GET['gift'] == 1) {
            $where['have_gift'] = 1;//是否有赠品
        }
        $where['goods_state']  = 1;//商品状态
        $where['goods_verify'] = 1;//商品审核状态
        $model_goods = Model('goods_rent');
        // $goods_list = $model_goods->getGoodsListByColorDistinct($where, $fieldstr, $order, 24);
        $goods_list = Model()->table("goods_rent")->field($fieldstr)->where($where)->order($order)->select();
        Tpl::output('show_page1', $model_goods->showpage(4));
        Tpl::output('show_page', $model_goods->showpage(5));
        // 商品多图
        if (!empty($goods_list)) {
            $commonid_array = array(); // 商品公共id数组
            $storeid_array = array();  // 店铺id数组
            foreach ($goods_list as $value) {
                $commonid_array[] = $value['goods_commonid'];
                $storeid_array[] = $value['store_id'];
            }
			//array_unique 移除数组中重复的值
            $commonid_array = array_unique($commonid_array);
            $storeid_array = array_unique($storeid_array);
            // 商品多图
            $goodsimage_more = $model_goods->getGoodsImageList(array('goods_commonid' => array('in', $commonid_array)));
            // 店铺
            $store_list = Model('store')->getStoreMemberIDList($storeid_array);

            foreach ($goods_list as $key => $value) {
                // 商品多图
                foreach ($goodsimage_more as $v) {
                    if ($value['goods_commonid'] == $v['goods_commonid'] && $value['store_id'] == $v['store_id'] && $value['color_id'] == $v['color_id']) {
                        $goods_list[$key]['image'][] = $v;
                    }
                }
                // 店铺的开店会员编号
                $store_id = $value['store_id'];
                $goods_list[$key]['member_id'] = $store_list[$store_id]['member_id'];
                $goods_list[$key]['store_domain'] = $store_list[$store_id]['store_domain'];
                //将关键字置红
                $goods_list[$key]['goods_name_highlight'] = $value['goods_name'];
            }
        }
        Tpl::output('goods_list', $goods_list);
        // 地区
        $province_array = Model('area')->getTopLevelAreas();
        Tpl::output('province_array', $province_array);

        loadfunc('search');
        /**
         * 取浏览过产品的cookie(最大四组)
         */
        $viewed_goods = Model('goods_browse')->getViewedGoodsList($_SESSION['member_id'],20);
        Tpl::output('viewed_goods',$viewed_goods);

        /**
         * 分类导航
         */
        $nav_link = array(
            0=>array(
                'title'=>$lang['homepage'],
                'link'=>SHOP_SITE_URL
            ),
            1=>array(
                'title'=>$lang['brand_index_all_brand'],
                'link'=>urlShop('brand', 'index')
            ),
            2=>array(
                'title'=>$brand_info['brand_name']
            )
        );
        Tpl::output('nav_link_list',$nav_link);
        /**
         * 页面输出
         */
        Tpl::output('index_sign','brand');

        Tpl::showpage('aa_goods');
    }

    public function goodsOp(){
		
        Language::read('store_goods_index');

        $goods_id = intval($_GET['goods_id']);

        // 商品详细信息

        $model_goods_rent = Model('goods_rent');
        $goods_detail = $model_goods_rent->getGoodsDetail($goods_id);
        //var_dump($goods_detail);exit;
        $goods_info = $goods_detail['goods_info'];
//      var_dump($goods_info);exit;
        if (empty($goods_info)) {
            showMessage(L('goods_index_no_goods'), '', 'html', 'error');
        }

        $model_order = Model('order');
        $rs = $model_order->getOrderNumAndOrderGoodsSalesRecordList(array('goods_commonid' => $goods_info['goods_commonid']));
        //var_dump($rs);exit;
        $count = 0;
        foreach ($rs as $v) {
            $count += $v['goods_num'];
        }
        $goods_info['goods_num'] = $count;
        // 看了又看（同分类本店随机商品）v3-b12
        $size = $goods_info['is_own_shop'] ? 8 : 4;
        $goods_rand_list = Model('goods')->getGoodsGcStoreRandList($goods_info['gc_id_1'], $goods_info['store_id'], $goods_info['goods_id'], $size);
        Tpl::output('goods_rand_list', $goods_rand_list);

        Tpl::output('spec_list', $goods_detail['spec_list']);
        Tpl::output('spec_image', $goods_detail['spec_image']);
        Tpl::output('goods_image', $goods_detail['goods_image']);
        Tpl::output('mansong_info', $goods_detail['mansong_info']);
        Tpl::output('gift_array', $goods_detail['gift_array']);

        // 生成缓存的键值
        $hash_key = $goods_info['goods_id'];
        $_cache = rcache($hash_key, 'product');
        if (empty($_cache)) {
            // 查询SNS中该商品的信息
            $snsgoodsinfo = Model('sns_goods')->getSNSGoodsInfo(array('snsgoods_goodsid' => $goods_info['goods_id']), 'snsgoods_likenum,snsgoods_sharenum');
            $data = array();
            $data['likenum'] = $snsgoodsinfo['snsgoods_likenum'];
            $data['sharenum'] = $snsgoodsinfo['snsgoods_sharenum'];
            // 缓存商品信息
            wcache($hash_key, $data, 'product');
        }
        $goods_info = array_merge($goods_info, $_cache);

        $inform_switch = true;
        // 检测商品是否下架,检查是否为店主本人
        if ($goods_info['goods_state'] != 1 || $goods_info['goods_verify'] != 1 || $goods_info['store_id'] == $_SESSION['store_id']) {
            $inform_switch = false;
        }
        Tpl::output('inform_switch', $inform_switch);

        // 如果使用售卖区域
        if ($goods_info['transport_id'] > 0) {
            // 取得三种运送方式默认运费
            $model_transport = Model('transport');
            $transport = $model_transport->getExtendList(array('transport_id' => $goods_info['transport_id'], 'is_default' => 1));
            if (!empty($transport) && is_array($transport)) {
                foreach ($transport as $v) {
                    $goods_info[$v['type'] . "_price"] = $v['sprice'];
                }
            }
        }
		// 取得商品详情的图片和参数
		$model_goods = Model('goods');
////		var_dump($_GET['goods_id']);exit;
		$result1 = $model_goods->getGoodsInfoAndPromotionById($_GET['goods_id']);
//      
        if (empty($result1)) {
            return null;
        }
        $result2 = $model_goods->getGoodeCommonInfoByID($result1['goods_commonid']);
        $goods_info1 = array_merge($result2, $result1);
//		var_dump($goods_info);exit;
		$goods_info['goods_attr'] = unserialize($goods_info1['goods_attr']);
		$goods_info['goods_body'] = $goods_info1['goods_body'];
		$goods_info['spec_name'] = unserialize($goods_info1['spec_name']);
		$goods_info['spec_value'] = unserialize($goods_info1['spec_value']);
//      echo '<pre>';
//		var_dump($goods_info);exit;
		
        Tpl::output('goods', $goods_info);
        //v3-b11 抢购商品是否开始
        $IsHaveBuy = 0;
        if (!empty($_SESSION['member_id'])) {
            $buyer_id = $_SESSION['member_id'];
            $promotion_type = $goods_info["promotion_type"];
            if ($promotion_type == 'groupbuy') {
                //检测是否限购数量
                $upper_limit = $goods_info["upper_limit"];
                if ($upper_limit >= 0) {    //限购数
                    //查询些会员的订单中，是否已买过了
                    $model_order = Model('order');
                    //取商品列表
                    $order_goods_list = $model_order->getOrderGoodsList1(array('goods_id' => $goods_id, 'order_goods.buyer_id' => $buyer_id, 'goods_type' => 6));
                    if ($order_goods_list) {
                        //取得上次购买的活动编号(防一个商品参加多次团购活动的问题)
                        $promotions_id = $order_goods_list[0]["promotions_id"];
                        //用此编号取数据，检测是否这次活动的订单商品。
                        $model_groupbuy = Model('groupbuy');
                        $groupbuy_info = $model_groupbuy->getGroupbuyInfo(array('groupbuy_id' => $promotions_id));
                        $num = 0;
                        for ($i = 0; $i < count($order_goods_list); $i++) {
                            $salenum += $order_goods_list[$i]['goods_num'];
                        }
                        if ($groupbuy_info && ($upper_limit > 0) && ($upper_limit <= $salenum)) {
                            $IsHaveBuy = 1;
                        } elseif ($groupbuy_info && ($upper_limit == 0)) {
                            $IsHaveBuy = 0;
                        } else {
                            $IsHaveBuy = 0;
                        }
                    }
                }
            }
        }
        Tpl::output('IsHaveBuy', $IsHaveBuy);
        $model_plate = Model('store_plate');
        // 顶部关联版式
        if ($goods_info['plateid_top'] > 0) {
            $plate_top = $model_plate->getStorePlateInfoByID($goods_info['plateid_top']);
            Tpl::output('plate_top', $plate_top);
        }
        // 底部关联版式
        if ($goods_info['plateid_bottom'] > 0) {
            $plate_bottom = $model_plate->getStorePlateInfoByID($goods_info['plateid_bottom']);
            Tpl::output('plate_bottom', $plate_bottom);
        }
        Tpl::output('store_id', $goods_info['store_id']);

        //推荐商品 v3-b12
        $goods_commend_list = $model_goods_rent->getGoodsOnlineList(array('store_id' => $goods_info['store_id'], 'goods_commend' => 1), 'goods_id,goods_name,goods_jingle,goods_image,store_id,goods_price', 0, 'rand()', 5, 'goods_commonid');
        Tpl::output('goods_commend', $goods_commend_list);

        // 当前位置导航
        $nav_link_list = Model('goods_class')->getGoodsClassNav($goods_info['gc_id'], 0);
        $nav_link_list[] = array('title' => $goods_info['goods_name']);
        Tpl::output('nav_link_list', $nav_link_list);

        //评价信息
        $goods_commonid = intval($goods_info['goods_commonid']);
        $model_order = Model('order');
        $order_snArray = $model_order->getOrderAndOrderGoodSevaluationRecordList(array('goods_commonid' => $goods_commonid));
        $a = '';
        foreach ($order_snArray as $k => $v) {
            $a .= ',' . $v['order_sn'];
        }
        $order_sn = explode(',', substr($a, 1));

        for ($i = 0; $i < count($order_sn); $i++) {
            $goods_evaluate_infoArray[$i] = Model('evaluate_goods')->getEvaluateGoodsInfoByOrderSn($order_sn[$i]);
        }
        $good = "";
        $normal = "";
        $bad = "";
        $all = "";
        $good_percent = "";
        $normal_percent = "";
        $bad_percent = "";
        $good_star = "";
        $star_average = "";

        foreach ($goods_evaluate_infoArray as $val) {
            $good += $val['good'];
            $normal += $val['normal'];
            $bad += $val['bad'];
            $all += $val['all'];
            $good_percent += $val['good_percent'];
            $normal_percent += $val['normal_percent'];
            $bad_percent += $val['bad_percent'];
            $good_star += $val['good_star'];
            $star_average += $val['star_average'];
        }
        if ($all > 0) {
            $goods_evaluate_info['good'] = $good;
            $goods_evaluate_info['normal'] = $normal;
            $goods_evaluate_info['bad'] = $bad;
            $goods_evaluate_info['all'] = $all;
            $goods_evaluate_info['good_percent'] = intval($good_percent / $all * 100);
            $goods_evaluate_info['normal_percent'] = intval($normal_percent / $all * 100);
            $goods_evaluate_info['bad_percent'] = intval($bad_percent / $all * 100);
            $goods_evaluate_info['good_star'] = $good_star;
            $goods_evaluate_info['star_average'] = $star_average / $all;
        } else {
            $goods_evaluate_info['good_percent'] = 100;
            $goods_evaluate_info['normal_percent'] = 0;
            $goods_evaluate_info['bad_percent'] = 0;
            $goods_evaluate_info['all'] = 0;
            $goods_evaluate_info['good_star'] = 5;
            $goods_evaluate_info['star_average'] = 5;
        }
        Tpl::output('goods_evaluate_info', $goods_evaluate_info);
        $seo_param = array();
        $seo_param['name'] = $goods_info['goods_name'];
        $seo_param['key'] = $goods_info['goods_keywords'];
        $seo_param['description'] = $goods_info['goods_description'];
        Model('seo')->type('product')->param($seo_param)->show();
		//店铺相关
		$store_id = $goods_info['store_id'];
		$store_model = Model('store');
        $store_info = $store_model->getStoreInfoByID($goods_info['store_id']);
		
		//cash_pledge 押金	rent_money 租金	rent_short_time 最短租期	rent_time 租赁次数
		
		//查找该商品的最短租期
		$model_rent = Model('goods_rent');
		$condition['goods_id'] = $_GET['goods_id'];
		$short_time = $model_rent->field('rent_short_time')->where($condition)->select();
//		var_dump($short_time);exit;
		Tpl::output('short_time',$short_time[0]['rent_short_time']);
		Tpl::output('groupbuy_start_time', TIMESTAMP + intval(C('groupbuy_review_day')) * 86400);
		Tpl::output('store_info', $store_info);
        Tpl::showpage('aa_goodsinfo');
    }


    public function editgoodsOp(){
//		echo '<pre>';
//  	var_dump($_POST);exit;
        $user_id = isset($_SESSION['member_id'])?$_SESSION['member_id']:'';
        if($_SESSION['is_login'] != 1){
            showDialog('请登录！');
        }
        $map['buyer_id']       = $user_id;//买家id
        $map['goods_id']      = isset($_POST['goods_id'])?$_POST['goods_id']:'';//商品id
        $map['buyer_name']    = isset($_POST['name'])?$_POST['name']:'';//买家姓名
        $map['buyer_phone']   = isset($_POST['tel'])?$_POST['tel']:'';//买家联系方式
        $map['buyer_address'] = isset($_POST['address'])?$_POST['address']:'';//买家地址
        $map['other']         = isset($_POST['other'])?$_POST['other']:'';//备注
        $map['add_time']      = time();//订单生成时间
		$map['units'] = isset($_POST['units'])?$_POST['units']:'';//单位名称
		$map['order_start'] = isset($_POST['order_start'])?strtotime($_POST['order_start']):'';//租赁开始时间
		$map['rent_time'] = $_POST['short_time'];//租期
		$map['order_amount'] = $_POST['price'];//订单首次要付的价格
		$map['store_id'] = $_POST['store_id'];//店铺id
//		数量  租金 押金
		$map['goods_num'] = $_POST['goods_num'];//商品数量
		$model_rent = Model('goods_rent');
		$condition['goods_id'] = $map['goods_id'];
		$info = $model_rent->field('cash_pledge,rent_money')->where($condition)->select();
		$map['order_pledge'] = $info[0]['cash_pledge'];//押金2
		$map['rent_price'] = $info[0]['rent_money'];//租金
		//计算订单到期时间
		if($map['rent_time'] == 6){
			$map['order_end'] = strtotime("+6 month",$map['order_start']);
		}else if($map['rent_time'] == 12){
			$map['order_end'] = strtotime("+12 month",$map['order_start']);
		}else if($map['rent_time'] == 18){
			$map['order_end'] = strtotime("+18 month",$map['order_start']);
		}
//		var_dump($map);exit;
		
        $result = Model()->table("rent_order")->insert($map);
     	if($result){
            showDialog('申请已提交！','','succ');
        }else{
            showDialog('提交失败！');
        }
    }

}
