<?php
/**
 * 政采云平台商品管理 v3-b12
 *
 */
defined('InShopNC') or exit('Access Invalid!');

class zcy_user_infoControl extends BaseSellerControl
{
    //需要对接政采云的店铺store_id
//	private $zcy_store = array(51,61);

    public function __construct()
    {
        parent::__construct();
        Language::read('member_store_goods_index');

    }

    public function indexOp()
    {
        $data = Model('zcy_config')->getSellerInfo(array('store_id'=>$_SESSION['store_id']));
        if(!empty($data) and $data['status'] == 1){
            Tpl::output('zcy_user_config', $data);
        }
        Tpl::showpage('zcy_user_info');
    }

    public function update_configOp()
    {
        if(empty($_POST['appkey'])){
            $res['code'] = -1;
            $res['msg'] = '请输入appkey';
            $res = json_encode($res);
            exit($res);
        }elseif(empty($_POST['appsecret'])){
            $res['code'] = -1;
            $res['msg'] = '请输入appsecret';
            $res = json_encode($res);
            exit($res);
        }
        $have = Model('zcy_config')->getSellerInfo(array('store_id'=>$_SESSION['store_id']));
        if(!empty($have)){
            $res_rel = Model('zcy_config')->editSeller(array('appkey'=>$_POST['appkey'],'appsecret'=>$_POST['appsecret'],'status'=>1),array('store_id'=>$_SESSION['store_id']));
            if($res_rel){
                $res['code'] = 1;
                $res['msg'] = '提交成功！';
                $res = json_encode($res);
                exit($res);
            }else{
                $res['code'] = -1;
                $res['msg'] = '提交失败，请重试！';
                $res = json_encode($res);
                exit($res);
            }
        }else{
            $res_rel = Model('zcy_config')->addSeller(array('appkey'=>$_POST['appkey'],'appsecret'=>$_POST['appsecret'],'status'=>1,'store_id'=>$_SESSION['store_id']));
            if($res_rel){
                $res['code'] = 1;
                $res['msg'] = '提交成功！';
                $res = json_encode($res);
                exit($res);
            }else{
                $res['code'] = -1;
                $res['msg'] = '提交失败，请重试！';
                $res = json_encode($res);
                exit($res);
            }
        }

    }



}


?>