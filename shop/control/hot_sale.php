<?php

/**
 * 热卖 v3-b12
 *
 * *by 好商城V3 www.haoid.cn 运营版 */
defined('InShopNC') or exit('Access Invalid!');

class hot_saleControl extends BaseHomeControl {

    //每页显示商品数
    const PAGESIZE = 36;

    //模型对象
    private $_model_search;

    public function __construct() {
        parent::__construct();
        Tpl::output('index_sign', '热卖');
    }

    public function indexOp() {
        Language::read('home_goods_class_index');
        $this->_model_search = Model('search');
        //显示左侧分类
        //默认分类，从而显示相应的属性和品牌
        $default_classid = intval($_GET['cate_id']);
        if (intval($_GET['cate_id']) > 0) {
            $goods_class_array = $this->_model_search->getLeftCategory(array($_GET['cate_id']));
        } elseif ($_GET['keyword'] != '') {
            //从TAG中查找分类
            $goods_class_array = $this->_model_search->getTagCategory($_GET['keyword']);
            //取出第一个分类作为默认分类，从而显示相应的属性和品牌
            $default_classid = $goods_class_array[0];
            $goods_class_array = $this->_model_search->getLeftCategory($goods_class_array, 1);
            ;
        }
        Tpl::output('goods_class_array', $goods_class_array);
        Tpl::output('default_classid', $default_classid);

        //优先从全文索引库里查找
        list($indexer_ids, $indexer_count) = $this->_model_search->indexerSearch($_GET, self::PAGESIZE);

        //获得经过属性过滤的商品信息 v3-b12
        list($goods_param, $brand_array, $initial_array, $attr_array, $checked_brand, $checked_attr) = $this->_model_search->getAttr($_GET, $default_classid);
        Tpl::output('brand_array', $brand_array);
        Tpl::output('initial_array', $initial_array);
        Tpl::output('attr_array', $attr_array);
        Tpl::output('checked_brand', $checked_brand);
        Tpl::output('checked_attr', $checked_attr);

        //处理排序
        $order = 'is_own_shop desc,goods_id desc';
        if (in_array($_GET['key'], array('1', '2', '3'))) {
            $sequence = $_GET['order'] == '1' ? 'asc' : 'desc';
            $order = str_replace(array('1', '2', '3'), array('goods_salenum', 'goods_click', 'goods_promotion_price'), $_GET['key']);
            $order .= ' ' . $sequence;
        }

        // 得到自定义导航信息
//        $nav_id = intval($_GET['nav_id']) ? intval($_GET['nav_id']) : 0;
//        Tpl::output('index_sign', $nav_id);
        // 地区
        $province_array = Model('area')->getTopLevelAreas();
        Tpl::output('province_array', $province_array);

        loadfunc('search');
        // 浏览过的商品
        $viewed_goods = Model('goods_browse')->getViewedGoodsList($_SESSION['member_id'], 20);
/////////////////
        //实例化商品Model
        $goods_model = Model('goods');
        //求出近一月的时间戳
        //$time = strtotime('-30 day');
        $time=3600*24*30;
        //echo $time;
        $data = time() - $time;
        //实例化订单表
        //$model = Model('order');
        //原生sql求出近一周销量最多商品的id
        //$sql = "select * from (select `goods_id`,sum(`goods_num`) as `total` from `zmkj_order_goods` inner join `zmkj_order` on `zmkj_order_goods`.`order_id` = `zmkj_order`.`order_id` where `finnshed_time` >= $time group by `goods_id`) as `goods` order by `total` desc limit 5;";
        //执行sql语句:条件--order:1个月;order_goods:数量最大;shuliang
        $sql = "select zog.goods_id,sum(zog.goods_num) as total from zmkj_order as zo"; //商品id/商品数量
        $sql .= " left join zmkj_order_goods as zog on zo.order_id=zog.order_id";
        $sql .= " where finnshed_time >= $data";
        $sql .= " group by zog.goods_id";
        $sql .= " order by total desc";
        $sql .= " limit 40";
        $goods_id1 = Model()->query($sql);
        //print_r($goods_id1);
        //对数据进行合并
        foreach ($goods_id1 as $k1 => $v1) {
            if (empty($v1['goods_id'])) {
                unset($goods_id1[$k1]);
            }
        }
        //进行排序
        $goods_id = array();
        foreach ($goods_id1 as $k2 => $v2) {
            $goods_id[] = $v2['goods_id'];
        }
        $where = "";
        for ($i = 0; $i <= count($goods_id) - 1; $i++) {
            $where .= "," . $goods_id[$i];
        }
        $where = substr($where, 1);
        $goods = $goods_model->query("select * from zmkj_goods where goods_id in ($where) and goods_state=1");
        //实例化店铺model
        $store_model = Model('store');
        //求出上方得出的所有店铺id
        $store_id = '';
        foreach ($goods as $value) {
            $store_id .= $value['store_id'] . '.';
        }
        //把最后的.去掉,然后把店铺id组成一个数组
        $store_id = rtrim($store_id, '.');
        $aa = explode('.', $store_id);
        //根据店铺id求出对应的店铺客服id
        $member = [];
        for ($i = 0; $i < count($aa); $i++) {
            $sql2 = "select `member_id` from zmkj_store where store_id = " . $aa[$i];
            $bb = $store_model->query($sql2);
            $member[] = $bb[0][member_id];
        }
        //把上方求出的客服id加入到goods数组里边
	if(!empty($goods)){
	        foreach ($member as $key => $value) {
        	    $goods[$key]['member_id'] = $value;
	        }
	}
        Tpl::output('goods', $goods);
        Tpl::output('show_page', $goods_model->showpage(5));
        Tpl::output('viewed_goods', $viewed_goods);
        Tpl::showpage('hot_sale');
    }

    /**
     * 获得推荐商品 v3-b12
     */
    public function get_hot_goodsOp() {
        $gc_id = $_GET['cate_id'];
        if ($gc_id <= 0) {
            return false;
        }
        // 获取分类id及其所有子集分类id
        $goods_class = Model('goods_class')->getGoodsClassForCacheModel();
        if (empty($goods_class[$gc_id])) {
            return false;
        }
        $child = (!empty($goods_class[$gc_id]['child'])) ? explode(',', $goods_class[$gc_id]['child']) : array();
        $childchild = (!empty($goods_class[$gc_id]['childchild'])) ? explode(',', $goods_class[$gc_id]['childchild']) : array();
        $gcid_array = array_merge(array($gc_id), $child, $childchild);
        // 查询添加到推荐展位中的商品id
        $boothgoods_list = Model('goods')->getGoodsOnlineList(array('gc_id' => array('in', $gcid_array)), 'goods_id', 4, 'rand()');
        if (empty($boothgoods_list)) {
            return false;
        }

        $goodsid_array = array();
        foreach ($boothgoods_list as $val) {
            $goodsid_array[] = $val['goods_id'];
        }

        $fieldstr = "goods_id,goods_commonid,goods_name,goods_jingle,store_id,store_name,goods_price,goods_promotion_price,goods_promotion_type,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_count";
        $goods_list = Model('goods')->getGoodsOnlineList(array('goods_id' => array('in', $goodsid_array)), $fieldstr);
        if (empty($goods_list)) {
            return false;
        }

        Tpl::output('goods_list', $goods_list);
        Tpl::showpage('goods.hot', 'null_layout');
    }

    /**
     * 获得同类商品排行
     */
    public function get_listhot_goodsOp() {
        $gc_id = $_GET['cate_id'];
        if ($gc_id <= 0) {
            return false;
        }
        // 获取分类id及其所有子集分类id
        $goods_class = Model('goods_class')->getGoodsClassForCacheModel();
        if (empty($goods_class[$gc_id])) {
            return false;
        }
        $child = (!empty($goods_class[$gc_id]['child'])) ? explode(',', $goods_class[$gc_id]['child']) : array();
        $childchild = (!empty($goods_class[$gc_id]['childchild'])) ? explode(',', $goods_class[$gc_id]['childchild']) : array();
        $gcid_array = array_merge(array($gc_id), $child, $childchild);
        // 查询添加到推荐展位中的商品id
        $boothgoods_list = Model('goods')->getGoodsOnlineList(array('gc_id' => array('in', $gcid_array)));
        if (empty($boothgoods_list)) {
            return false;
        }

        $goodsid_array = array();
        foreach ($boothgoods_list as $val) {
            $goodsid_array[] = $val['goods_id'];
        }

        $fieldstr = "goods_id,goods_commonid,goods_name,goods_jingle,store_id,store_name,goods_price,goods_promotion_price,goods_promotion_type,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_count";
        $goods_list = Model('goods')->getGoodsOnlineList(array('goods_id' => array('in', $goodsid_array)), $fieldstr, 5, 'goods_salenum desc');
        if (empty($goods_list)) {
            return false;
        }

        Tpl::output('goods_list', $goods_list);
    }

    /**
     * 获得推荐商品
     */
    public function get_booth_goodsOp() {
        $gc_id = $_GET['cate_id'];
        if ($gc_id <= 0) {
            return false;
        }
        // 获取分类id及其所有子集分类id
        $goods_class = Model('goods_class')->getGoodsClassForCacheModel();
        if (empty($goods_class[$gc_id])) {
            return false;
        }
        $child = (!empty($goods_class[$gc_id]['child'])) ? explode(',', $goods_class[$gc_id]['child']) : array();
        $childchild = (!empty($goods_class[$gc_id]['childchild'])) ? explode(',', $goods_class[$gc_id]['childchild']) : array();
        $gcid_array = array_merge(array($gc_id), $child, $childchild);
        // 查询添加到推荐展位中的商品id
        $boothgoods_list = Model('p_booth')->getBoothGoodsList(array('gc_id' => array('in', $gcid_array)), 'goods_id', 0, 4, 'rand()');
        if (empty($boothgoods_list)) {
            return false;
        }

        $goodsid_array = array();
        foreach ($boothgoods_list as $val) {
            $goodsid_array[] = $val['goods_id'];
        }

        $fieldstr = "goods_id,goods_commonid,goods_name,goods_jingle,store_id,store_name,goods_price,goods_promotion_price,goods_promotion_type,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_count";
        $goods_list = Model('goods')->getGoodsOnlineList(array('goods_id' => array('in', $goodsid_array)), $fieldstr);
        if (empty($goods_list)) {
            return false;
        }

        Tpl::output('goods_list', $goods_list);
        Tpl::showpage('goods.booth', 'null_layout');
    }

    public function auto_completeOp() {
        try {
            require(BASE_DATA_PATH . '/api/xs/lib/XS.php');
            $obj_doc = new XSDocument();
            $obj_xs = new XS(C('fullindexer.appname'));
            $obj_index = $obj_xs->index;
            $obj_search = $obj_xs->search;
            $obj_search->setCharset(CHARSET);
            $corrected = $obj_search->getExpandedQuery($_GET['term']);
            if (count($corrected) !== 0) {
                $data = array();
                foreach ($corrected as $word) {
                    $row['id'] = $word;
                    $row['label'] = $word;
                    $row['value'] = $word;
                    $data[] = $row;
                }
                exit(json_encode($data));
            }
        } catch (XSException $e) {
            if (is_object($obj_index)) {
                $obj_index->flushIndex();
            }
//             Log::record('search\auto_complete'.$e->getMessage(),Log::RUN);
        }
    }

    /**
     * 获得猜你喜欢
     */
    public function get_guesslikeOp() {
        $goodslist = Model('goods_browse')->getGuessLikeGoods($_SESSION['member_id'], 20);
        if (!empty($goodslist)) {
            Tpl::output('goodslist', $goodslist);
            Tpl::showpage('goods_guesslike', 'null_layout');
        }
    }

}
