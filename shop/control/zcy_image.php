<?php
/**
 * 政采云平台商品管理 v3-b12
 *
 */
defined('InShopNC') or exit('Access Invalid!');

class zcy_imageControl extends BaseSellerControl
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

    }

    public function indexOp()
    {
        /**
         * 分页类
         */
        $page = new Page();
        $page->setEachNum(100);
        $page->setStyle('admin');

        $model_album = Model('album');
//        var_dump($model_album);die;
        /**
         * 验证是否存在默认相册
         */
        $return = $model_album->getPicList(['store_id'=>$_SESSION["store_id"]],$page);

        Tpl::output('img',$return);
        Tpl::output('show_page', $page->show());
        Tpl::showpage('zcy_img');
    }


    public function upgoodsOp(){
        if($_POST){
            require_once(BASE_PATH . '/../zcy/nr_zcy.php');
            $zcy = new nr_zcy();
//            $_POST['path'][0] = 'avatar_45.jpg';
//            unset($_POST['path'][1]);
//            $_POST['path'] = implode(',',$_POST['path']);
            $img = 'F:\phpstudy_pro/WWW/zcy/data/upload/shop/store/goods/51/'.implode(',',$_POST['path']);

            $data = $zcy->zcyimage($img,time());
            print_r($data);
        }
    }
    

}

?>