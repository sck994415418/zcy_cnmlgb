<?php

/**
 * 会员中心——卖家追加评价
 */
defined('InShopNC') or exit('Access Invalid!');

class store_evaluate_secondControl extends BaseSellerControl {

    public function __construct() {
        parent::__construct();
        Language::read('member_layout,member_evaluate');
        Tpl::output('pj_act', 'store_evaluate');
    }

    /**
     * 追加评价列表
     */
    public function listOp() {
        $model_evaluate_goods = Model('evaluate_goods');
        $condition = array();
        if (!empty($_GET['goods_name'])) {
            $condition['geval_goodsname'] = array('like', '%' . $_GET['goods_name'] . '%');
        }
        if (!empty($_GET['member_name'])) {
            $condition['geval_frommembername'] = array('like', '%' . $_GET['member_name'] . '%');
        }
        $condition['geval_storeid'] = $_SESSION['store_id'];//登入商店，将商店id放入session
        $goodsevallist = $model_evaluate_goods->getEvaluateGoodsList($condition, 10, 'geval_id desc');
        
    //过滤掉没有“追加评论”的内容。
//        print_r($goodsevallist);
        foreach($goodsevallist as $key=>$val){
            if(empty($val['geval_second_content'])){
                unset($goodsevallist[$key]);
            }
        }
            
        Tpl::output('goodsevallist', $goodsevallist);   
        Tpl::output('show_page', $model_evaluate_goods->showpage());
        Tpl::showpage('evaluation_second.index');
    }

    /**
     * 解释来自买家的追加评价
     */
    public function explain_saveOp() {
        $geval_id = intval($_POST['geval_id']); //解释id
        $geval_second_explain = trim($_POST['geval_second_explain']); //解释内容
        //验证表单
        if (!$geval_second_explain) {
            $data['result'] = false;
            $data['message'] = '解释内容不能为空';
            echo json_encode($data);
            die;
        }
        $data = array();
        $data['result'] = true;

        $model_evaluate_goods = Model('evaluate_goods');

        $evaluate_info = $model_evaluate_goods->getEvaluateGoodsInfoByID($geval_id, $_SESSION['store_id']);
        if (empty($evaluate_info)) {
            $data['result'] = false;
            $data['message'] = L('param_error');
            echo json_encode($data);
            die;
        }
        //更新操作。
        $update = array('geval_second_explain' => $geval_second_explain);
        $condition = array('geval_id' => $geval_id);
        $result = $model_evaluate_goods->editEvaluateGoods($update, $condition);
        if ($result) {
            $data['message'] = '解释成功';
        } else {
            $data['result'] = false;
            $data['message'] = '解释保存失败';
        }
        echo json_encode($data);
        die;
    }

}
