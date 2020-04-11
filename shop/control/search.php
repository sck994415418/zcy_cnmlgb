<?php

/**
 * 商品列表 v3-b12
 *
 * *by 好商城V3 www.haoid.cn 运营版 */
defined('InShopNC') or exit('Access Invalid!');

class searchControl extends BaseHomeControl {

    //每页显示商品数
    const PAGESIZE = 36;

    //模型对象
    private $_model_search;

    /*
     * 默认op=index
     */

    public function indexOp() {
        Language::read('home_goods_class_index');
        $this->_model_search = Model('search');
        //显示左侧分类
        //默认分类，从而显示相应的属性和品牌
        $default_classid = intval($_GET['cate_id']); //没有，默认为0
        if (intval($_GET['cate_id']) > 0) {
            $goods_class_array = $this->_model_search->getLeftCategory(array($_GET['cate_id']));
        } elseif ($_GET['keyword'] != '') {
            //从TAG中查找分类
            $goods_class_array = $this->_model_search->getTagCategory($_GET['keyword']);
            //取出第一个分类作为默认分类，从而显示相应的属性和品牌
            $default_classid = $goods_class_array[0];
            $goods_class_array = $this->_model_search->getLeftCategory($goods_class_array, 1);
        }
        Tpl::output('goods_class_array', $goods_class_array);
        Tpl::output('default_classid', $default_classid);
        //优先从全文索引库里查找
        //print_r($_GET);die;
        //print_r($this->_model_search->indexerSearch($_GET, self::PAGESIZE));die;
        list($indexer_ids, $indexer_count) = $this->_model_search->indexerSearch($_GET, self::PAGESIZE);
        //获得经过属性过滤的商品信息 v3-b12
        list($goods_param, $brand_array, $initial_array, $attr_array, $checked_brand, $checked_attr) = $this->_model_search->getAttr($_GET, $default_classid);
        Tpl::output('brand_array', $brand_array);
        //print_r($brand_array);
        Tpl::output('initial_array', $initial_array);
        //Tpl::output('attr_array', $attr_array);
        //print_r($attr_array);
        Tpl::output('checked_brand', $checked_brand);
        Tpl::output('checked_attr', $checked_attr);

        //处理排序
        $order = 'is_own_shop desc,goods_id desc';
        if (in_array($_GET['key'], array('1', '2', '3'))) {
            $sequence = $_GET['order'] == '1' ? 'asc' : 'desc';
            $order = str_replace(array('1', '2', '3'), array('goods_salenum', 'goods_click', 'goods_promotion_price'), $_GET['key']);
            $order .= ' ' . $sequence;
        }
        $model_goods = Model('goods');
        // 字段
        $fields = "goods_id,goods_commonid,goods_name,goods_jingle,gc_id,store_id,store_name,goods_price,goods_promotion_price,vipprice,gjprice,goods_promotion_type,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_good_star,evaluation_count,is_virtual,is_fcode,is_appoint,is_presell,have_gift";
        //条件
        $condition = array();
        if (is_array($indexer_ids)) {
            //商品主键搜索
            $condition['goods_id'] = array('in', $indexer_ids);
            $goods_list = $model_goods->getGoodsOnlineList($condition, $fields, 0, $order, self::PAGESIZE, null, false);
            //print_r($goods_list);
            //如果有商品下架等情况，则删除下架商品的搜索索引信息
            if (count($goods_list) != count($indexer_ids)) {
                $this->_model_search->delInvalidGoods($goods_list, $indexer_ids);
            }
            pagecmd('setEachNum', self::PAGESIZE);
            pagecmd('setTotalNum', $indexer_count);
        } else {
            //执行正常搜索
            // header("content-type:text/html;charset=utf-8");
            if (isset($goods_param['class'])) {
                $condition['gc_id_' . $goods_param['class']['depth']] = $goods_param['class']['gc_id'];
            }
            if (intval($_GET['b_id']) > 0) { //品牌
                $condition['brand_id'] = intval($_GET['b_id']);
            }
            /**
             * 首先判断有没有空格，有空格，
             */
            $keyword = trim($_GET['keyword']);
            // 有中文的时候：有英文、
            if ($keyword != '') { //关键词
                if ($rs = strpos($keyword, ' ')) {
                    $keyword = preg_replace("/(\s+)/", "%", $keyword);
                } else {
                    $keyword = preg_replace("/([0-9]+)/", "%$1", $keyword);
                }
                // echo $keyword;die;
                $condition['goods_name|goods_jingle'] = array('like', '%' . $keyword . '%'); //商品name或者商品广告词
            }
            if (intval($_GET['area_id']) > 0) {  //地区
                $condition['areaid_1'] = intval($_GET['area_id']);
            }
            if ($_GET['type'] == 1) {
                $condition['is_own_shop'] = 1;
            }
            if ($_GET['gift'] == 1) {
                $condition['have_gift'] = 1;
            }
            if (isset($goods_param['goodsid_array'])) {
                $condition['goods_id'] = array('in', $goods_param['goodsid_array']);
            }
            //v3-b13 按价格搜索
            if (intval($_GET['priceMin']) >= 0) {
                $condition['goods_price'] = array('egt', intval($_GET['priceMin']));
            }
            if (intval($_GET['priceMax']) >= 0) {
                $condition['goods_price'] = array('elt', intval($_GET['priceMax']));
            }
            if (intval($_GET['priceMin']) >= 0 && intval($_GET['priceMax']) >= 0) {
                $condition['goods_price'] = array('between', array(intval($_GET['priceMin']), intval($_GET['priceMax'])));
            }
            //v3-b13 end
            $goods_list = $model_goods->getGoodsListByColorDistinct($condition, $fields, $order, self::PAGESIZE); //goods表.
        }

        Tpl::output('show_page1', $model_goods->showpage(4));
        Tpl::output('show_page', $model_goods->showpage(5));

        // print_r($goods_list);
        // 商品多图
        if (!empty($goods_list)) {
            $commonid_array = array(); // 商品公共id数组
            $storeid_array = array();       // 店铺id数组
            $goods_array = array();
            foreach ($goods_list as $value) {
                $commonid_array[] = $value['goods_commonid']; //将commonid放到一个单独的数据中..
                $storeid_array[] = $value['store_id']; //将goods_id放到一个单独的数组中.
                $goods_array[$value['goods_commonid']][] = $value['goods_id'];
            }
            //过滤
            $commonid_array = array_unique($commonid_array); //去除重复的,只保留第一个commonid.
            $storeid_array = array_unique($storeid_array); //去除重复的,只保留第一个store_id
            //根据goods_id获取商品图片;commonid为空.
            // $goodsimage_more1 = Model('goods')->getGoodsImageList(array('goods_id' => array('in', $goods_array)));
            // 根据commonid,获取商品多图;goods_id为空;
            $goodsimage_more = Model('goods')->getGoodsImageList(array('goods_commonid' => array('in', $commonid_array)));
            //print_r(Log::read());
            // 根据商店id,获取店铺信息:店铺id,开店铺的会员id,
            $goodsimage_more3 = array();
            foreach ($goodsimage_more as $value1) {
                $goodsimage_more3[$value1['goods_commonid']][] = $value1;
            }
            //选出commonid图片小于3个的;将commonid赋值到数组中.
            $commonid_array1 = array();
            foreach ($goodsimage_more3 as $key2 => $value2) {
                //commonid图片<3张，则调取goods_id;
                if (count($goodsimage_more3[$key2]) > 2) {
                    unset($goods_array[$key2]); //得到goods_id.
                } else {
                    //如果图片小图或者等于3张，则去掉，调用goods_id图片。
                    foreach ($goodsimage_more as $key3 => $value3) {
                        if ($value3['goods_commonid'] == $key2) {
                            unset($goodsimage_more[$key3]);
                        }
                    }
                }
            }
            $goods_array1 = array();
            foreach ($goods_array as $key4 => $value4) {
                $goods_array1[$key4] = $value4[0];
            }
            $goodsimage_more1 = Model('goods')->getGoodsImageList(array('goods_id' => array('in', $goods_array1)));
            $store_list = Model('store')->getStoreMemberIDList($storeid_array);
            //搜索的关键字
            $search_keyword = trim($_GET['keyword']);
            foreach ($goods_list as $key5 => $value5) { //goods表内容
                // 商品多图
                //v3-b11 商品列表主图限制不越过5个
                $m = 0;
                foreach ($goodsimage_more1 as $k1 => $v1) {
                    if ($value5['goods_id'] == $v1['goods_id'] && $value5['store_id'] == $v1['store_id'] && $value5['color_id'] == $v1['color_id']) {
                        $m++;
                        $goods_list[$key5]['image'][] = $v1;
                        if ($m >= 5)
                            break;
                    }
                }
                $n = 0;
                foreach ($goodsimage_more as $k => $v) {
                    if ($value5['goods_commonid'] == $v['goods_commonid'] && $value5['store_id'] == $v['store_id'] && $value5['color_id'] == $v['color_id']) {
                        $n++;
                        $goods_list[$key5]['image'][] = $v;
                        if ($n >= 5)
                            break;
                    }
                }
                // 店铺的开店会员编号
                $store_id = $value5['store_id']; //商品所属的店铺
                $goods_list[$key5]['member_id'] = $store_list[$store_id]['member_id']; //以店铺id为键的所有信息.
                $goods_list[$key5]['store_domain'] = $store_list[$store_id]['store_domain'];
                //将关键字置红
                if ($search_keyword) {
                    $goods_list[$key5]['goods_name_highlight'] = str_replace($search_keyword, '<font style="color:#f00;">' . $search_keyword . '</font>', $value5['goods_name']);
                } else {
                    $goods_list[$key5]['goods_name_highlight'] = $value5['goods_name'];
                }
            }
        }
        Tpl::output('goods_list', $goods_list);
        /**
         * 在搜索栏中展示关键词
         */
        if ($_GET['keyword'] != '') {
            Tpl::output('show_keyword', $_GET['keyword']);
        } else {
            Tpl::output('show_keyword', $goods_param['class']['gc_name']);
        }

        $model_goods_class = Model('goods_class');

        // html--title方法
        if ($_GET['keyword'] == '') {
            $seo_class_name = $goods_param['class']['gc_name'];
            if (is_numeric($_GET['cate_id']) && empty($_GET['keyword'])) {
                $seo_info = $model_goods_class->getKeyWords(intval($_GET['cate_id']));
                if (empty($seo_info[1])) {
                    $seo_info[1] = C('site_name') . ' - ' . $seo_class_name;
                }
                Model('seo')->type($seo_info)->param(array('name' => $seo_class_name))->show();
            }
        } elseif ($_GET['keyword'] != '') {
            Tpl::output('html_title', (empty($_GET['keyword']) ? '' : $_GET['keyword'] . ' - ') . C('site_name') . L('nc_common_search'));
        }

        // 当前位置导航
        $nav_link_list = $model_goods_class->getGoodsClassNav(intval($_GET['cate_id']));
        Tpl::output('nav_link_list', $nav_link_list);

        // 得到自定义导航信息
        $nav_id = intval($_GET['nav_id']) ? intval($_GET['nav_id']) : 0;
        Tpl::output('index_sign', $nav_id);

        // 地区
        $province_array = Model('area')->getTopLevelAreas();
        Tpl::output('province_array', $province_array);

        loadfunc('search');

        // 浏览过的商品
        $viewed_goods = Model('goods_browse')->getViewedGoodsList($_SESSION['member_id'], 20);
        Tpl::output('viewed_goods', $viewed_goods);
        Tpl::showpage('search');
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

        $fieldstr = "goods_id,goods_commonid,goods_name,goods_jingle,store_id,store_name,goods_price,goods_promotion_price,goods_promotion_type,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_count,vipprice,gjprice";
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

    /**
     * 设置自定义词典内容
     */
    public function set_dictOp() {
        try {
            require(BASE_DATA_PATH . '/api/xs/lib/XS.php');
            $obj_doc = new XSDocument();
            $obj_xs = new XS(C('fullindexer.appname'));
            $obj_index = $obj_xs->index; //构建索引
            $obj_search = $obj_xs->search; //构建查询
            $obj_search->setCharset(CHARSET);
            $index = new XSIndex();
            print_r($index->setCustomDict("复印机 14.88 5.59 n"));
        } catch (XSException $e) {
            print_r($e->__toString());
//             Log::record('search\auto_complete'.$e->getMessage(),Log::RUN);
        }
    }

    /**
     * 获取自定义词典内容
     */
    public function get_dictOp() {
        try {
            require(BASE_DATA_PATH . '/api/xs/lib/XS.php');
            $obj_doc = new XSDocument();
            $obj_xs = new XS(C('fullindexer.appname'));
            $obj_index = $obj_xs->index; //构建索引
            $obj_search = $obj_xs->search; //构建查询
            $obj_search->setCharset(CHARSET);
            $index = new XSIndex();
            print_r($index->getCustomDict());
        } catch (XSException $e) {
            print_r($e->__toString());
        }
    }

}
