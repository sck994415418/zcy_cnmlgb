<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/4/11
 * Time: 10:40
 */
/**
 * 政采云平台商品管理 v3-b12
 *
 */
defined('InShopNC') or exit('Access Invalid!');
class zcy_commonControl extends Model {
    public function aa()
    {
        $data = Model('zcy_config')->getSellerInfo(array('store_id'=>$_SESSION['store_id']));
        if(empty($data)){
            $res = false;
        }elseif($data['status'] != 1){
            $res = false;
        }else{
            $_SESSION['zcy_user_config'] = $data;
            return $data;
        }
        if($res==false){
            exit("当前店铺没有此权限！请<a href=\"/shop/index.php?act=zcy_user_info&op=index\">完善信息</a>进行申请");
        }
    }
}