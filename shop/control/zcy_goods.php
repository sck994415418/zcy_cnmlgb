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
        include_once 'zcy_common.php';
        $res = new zcy_commonControl();
        $res->aa();
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
                $model = Model();
                $spu = $model->table("zcy_category")->where(['pid'=>0])->limit(false)->select();
                $goods = $model->table('goods')->where(['store_id'=>$_SESSION["store_id"]])->page(20)->order('goods_id desc')->select();
//                echo '<pre>';
//                print_r($spu);die;
                Tpl::output("goods_class", $spu);
                Tpl::output("rs_array", $goods);
                Tpl::output("page", $model->showpage(2));
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
            array('menu_key' => 'zcy_goods_verify', 'menu_name' => "待审核商品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'verify'))),
            array('menu_key' => 'zcy_goods_offshelf', 'menu_name' => "已下架商品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'off_shelf'))),
            array('menu_key' => 'zcy_goods_freez', 'menu_name' => "已冻结商品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'freez'))),
            array('menu_key' => 'zcy_goods_refuse', 'menu_name' => "审核不通过商品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'refuse'))),
            array('menu_key' => 'zcy_goods_cloud', 'menu_name' => "未上传新品", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'zcy_addgoods'))),
        );
        Tpl::output('member_menu', $menu_array);
        Tpl::output('menu_key', $menu_key);
    }


    //添加商品
    public function add_goodsOp()
    {
        $where = [
            'store_id'=>$_SESSION['store_id'],
            'goods_state'=>1,
            'goods_verify'=>1,
        ];
        if (intval($_GET['zcy_category']) > 0) {
            $where['zcy_category']=intval($_GET['zcy_category']);
        }
        if (trim($_GET['is_colud']) == 'true') {
            $where['is_cloud']=1;
        }
        if (trim($_GET['keyword']) != '') {
            switch ($_GET['search_type']) {
                case "0":
                    $keyword = str_replace(" ", "%", trim($_GET["keyword"]));
                    $where['goods_name']= ['like', '%'.$keyword.'%'];
                    break;
                case "1":
                    $where['goods_id']=intval(trim($_GET['keyword']));
                    break;
                case "2":
                    $where['goods_commonid']=intval($_GET['keyword']);
                    break;
            }
        }
        if (trim($_GET['order']) != '') {
            switch (trim($_GET['order'])) {
                case "1":

                    $order =  " goods_id desc";
                    break;
                case "2":
                    $order =  " goods_id asc";
                    break;
                case "3":
                    $order =  "goods_commonid desc";
                    break;
                case "4":
                    $order =  "goods_commonid asc";
                    break;
                case "5":
                    $order = "goods_edittime desc";
                    break;
                case "6":
                    $order = "goods_edittime asc";
                    break;
                default:
                    $order = "goods_id desc";
            }
        } else {
            $order = "goods_id desc";
        }
        $model = Model();
        $rs_array = $model->table('goods')->where($where)->page(10)->order($order)->select();
        Tpl::output("rs_array", $rs_array);

        Tpl::output("page",$model->showpage(2));
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

            $model = Model();
            $spu = $model->table("zcy_category")->where(['pid'=>0])->limit(false)->select();
            $img = $model->table("zcy_img")->order("id desc")->page(20)->select();
            $sql = "select * from `zmkj_zcy_brand`";
            $res = $model->query($sql);
            Tpl::output("goods_brand", json_encode($res));
            Tpl::output("goods_class", $spu);
            Tpl::output("imgdata", $img);
            Tpl::output('page',$model->showpage(2));

            Tpl::showpage('zcy_goods');

    }

    public function zcy_goodsdataOp(){
        $model = Model();
        echo "<pre>";
        $_POST['layer'] = 11;
        $goods = $model->table('goods')->where(["goods_id"=>$_POST['goods_id']])->field("goods_name,goods_price,goods_marketprice,goods_storage")->find();
        $_POST['skus'] =[
            'price'=>$goods['goods_price']*100,
            'attrs'=>['']
        ];
        print_r($_POST);die;

        require_once(BASE_PATH . '/../zcy/nr_zcy.php');
        $zcy = new nr_zcy();

        $rs = $zcy->create_goods();
    }
    public function linkageOp(){
        $model = Model();
        $spu = $model->table("zcy_category")->where(['pid'=>$_GET['id']])->limit(false)->select();
        die(json_encode($spu));
    }

    public function categoryOp(){
        $_GET['goods_id'];
        require_once(BASE_PATH . '/../zcy/nr_zcy.php');
        $zcy = new nr_zcy();
        $attr = $zcy->get_category_attrs($_GET['goods_id']);
        die(json_encode($attr));
    }

    public function provinceIdOp(){
        $model = Model();
        $spu = $model->table("zcy_address_code")->limit(false)->select();
        die(json_encode($spu));
    }
}

?>