<?php
/**
 * 政采云平台商品管理 v3-b12
 *
 */
defined('InShopNC') or exit('Access Invalid!');

class zcy_goodsControl extends BaseSellerControl
{
    //需要对接政采云的店铺store_id
    private $zcy_store = array(51, 61, 1);
    private $nrzcy = null;

    public function __construct()
    {
        parent::__construct();
        Language::read('member_store_goods_index');
        //验证是否有政采云操作权限
        if (!in_array($_SESSION["store_id"], $this->zcy_store)) {
            exit("当前店铺没有此权限！请<a href=\"/shop/index.php?act=seller_center&op=index\">返回</a>");
        }
        $model = Model("zcy_category");
        $spu = $model->where(['pid' => 0])->limit(false)->select();
        Tpl::output("goods_class", $spu);

    }

    public function indexOp()
    {
        $this->zcy_goods_listOp();
    }

    /*
     *政采云平台商品列表
     *
     */
    public function zcy_goods_listOp()
    {

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
            case 'zcy_addgoods':
                $this->profile_menu('zcy_goods_cloud');
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
            case 'zcy_addgoods':// 主动映射的商品改价
                $model = Model("zcy_category");
                $spu = $model->where(['pid' => 0])->limit(false)->select();
                Tpl::output("goods_class", $spu);
                Tpl::showpage('zcy_addgoods');
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
    private function profile_menu($menu_key = '')
    {
        $menu_array = array(
            array('menu_key' => 'zcy_goods_onshelf', 'menu_name' => "已上架商品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'on_shelf'))),
            array('menu_key' => 'zcy_goods_cloud', 'menu_name' => "待审核商品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'zcy_addgoods'))),
            array('menu_key' => 'zcy_goods_offshelf', 'menu_name' => "已下架商品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'off_shelf'))),
            array('menu_key' => 'zcy_goods_freez', 'menu_name' => "已冻结商品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'freez'))),
            array('menu_key' => 'zcy_goods_refuse', 'menu_name' => "审核不通过商品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'refuse'))),
        );
        Tpl::output('member_menu', $menu_array);
        Tpl::output('menu_key', $menu_key);
    }


    //添加商品
    public function add_goodsOp()
    {
        include_once "store_goods_change_price.php";
        $zf_url = new zf_url();
        $tj = "`store_id` = " . $_SESSION['store_id'] . " and `goods_state` = 1 and `goods_verify` = 1";
//        die;
        if (intval($_GET['zcy_category']) > 0) {
            $tj = $tj . " and `zcy_category` = " . intval($_GET['zcy_category']);
        }
        if (trim($_GET['is_cloud']) == 'true') {
            $tj = $tj . " and `is_cloud` > 0";
        }
        if (trim($_GET['keyword']) != '') {
            switch ($_GET['search_type']) {
                case "0":
                    $keyword = str_replace(" ", "%", trim($_GET["keyword"]));
                    $tj = $tj . " and `goods_name` like '%" . $keyword . "%'";
                    break;
                case "1":
                    $tj = $tj . " and `goods_id` = " . intval(trim($_GET['keyword']));
                    break;
                case "2":
                    $tj = $tj . " and `goods_commonid` = " . intval($_GET['keyword']);
                    break;
                case "3":
                    $tj = $tj . " and (`goods_id` IN (select DISTINCT `skuid` from `zmkj_goods_orm` where `productId` like '%" . trim($_GET['keyword']) . "%') or `goods_id` IN (select DISTINCT `goods_id` from `zmkj_zf_url` where `zf_product_id` like '%" . trim($_GET['keyword']) . "%'))";
            }
        }
        if (trim($_GET['order']) != '') {
            switch (trim($_GET['order'])) {
                case "1":
                    $tj = $tj . " order by `goods_id` desc";
                    break;
                case "2":
                    $tj = $tj . " order by `goods_id` asc";
                    break;
                case "3":
                    $tj = $tj . " order by `goods_commonid` desc";
                    break;
                case "4":
                    $tj = $tj . " order by `goods_commonid` asc";
                    break;
                case "5":
                    $tj = $tj . " order by `goods_edittime` desc";
                    break;
                case "6":
                    $tj = $tj . " order by `goods_edittime` asc";
                    break;
                default:
                    $tj = $tj . " order by `goods_id` desc";
            }
        } else {
            $tj = $tj . " order by `goods_id` desc";
        }
        $sqlall = "select count(*) from `zmkj_goods` where {$tj}";//获取总条数
        $resultall = $zf_url->select_data($sqlall);
        $c = $resultall[0]["count(*)"];//获取总条数
        $page = new page($c, 50);//一共多少条 每页显示多少条
        $sql = "select * from `zmkj_goods` where {$tj} " . $page->limit;
        $rs_array = $zf_url->select_data($sql);
        Tpl::output("rs_array", $rs_array);
        Tpl::output("page", $page);
        $this->profile_menu("zcy_goods_cloud");
        Tpl::showpage("zcy_addgoods");

    }


    // 编辑商品
    public function edit_goodsOp()
    {

    }

    //上传政采云商品
    public function zcy_goodsOp()
    {
        $_GET['goods_id'];
        require_once(BASE_PATH . '/../zcy/nr_zcy.php');
        $zcy = new nr_zcy();
        $attr = $zcy->get_category_attrs('1174');
        echo '<pre>';
        print_r($attr);
        die;
        $goods = [
            'otherAttributes' => [
                'attrVal' => '',
                'attrKey' => '',
                'propertyId' => '',
            ],
            'layer' => 11,
            "skus" => [
                [
                    "quantity" => 100000,
                    "price" => 10000,
                    "platformPrice" => 20000,
                    "skuCode" => "123456",
                    "attrs" => [
                        "颜色分类" => "白"
                    ],

                ],
                'item' => [
                    'limit' => 0,
                    'selfPlatformLink' => $url,
                    'itemCode' => $goodsid,
                    'mainImage' => $img,
                    'origin' => "",
                    'countryId' => "",//国家编码
                    'provinceId' => "",//省份编码
                    'cityId' => "",//城市编码
                    'regionId' => "",//地区编码
                    'name' => "",
                    'categoryId' => "",

                ],
            ],
        ];
        $rs = $zcy->create_goods($goods);
    }

}

?>